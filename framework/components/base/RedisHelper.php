<?php

namespace BaseComponents\base;

/**
 * Redis操作助手
 * @author bo.lin
 */
class RedisHelper
{

    /**
     * 通过回调方法获取数据
     * @param string $key 缓存key
     * @param \Closure|string|array $getter 回调函数
     * @param array $params 回调函数参数
     * @param int $timeout 超时时间
     * @param bool $retry 是否重新调用
     * @return mixed
     * @throws Exception
     */
    public static function getValue($key, $getter, array $params = array(), $timeout = 24 * 60 * 60, $retry = true)
    {
        $redis = self::getRedis();
        $data = $redis->get($key);

        if (empty($data)) {
            $result = call_user_func_array($getter, $params);

            if ($result === false) {
                throw new Exception(ErrorCode::ERR_EMPTY_RESULT[0], '回调函数无返回！');
            }

            $redis->set($key, serialize($result), $timeout);

            return $result;
        } else {
            try {
                return unserialize($data);
            } catch (\Exception $e) {
                $redis->del($key);
            }

            if ($retry) {
                return self::getValue($key, $getter, $params, $timeout, false);
            }
        }

        throw new Exception(ErrorCode::ERR_EMPTY_RESULT[0], '回调函数无返回！');
    }

    /**
     * 通过回调方法获取Hash数据
     * @param string $key RedisKey
     * @param string $hashKey hash指定的key
     * @param $getter \Closure|string|array 回调函数
     * @param array $params 回调函数所需参数
     * @param int $timeout 超时(仅生效redisKey)
     * @param bool $retry 是否与重试
     * @return mixed
     * @throws Exception
     */
    public static function getHashValue($key, $hashKey, $getter, array $params = array(), $timeout = 24 * 60 * 60, $retry = true)
    {
        $redis = self::getRedis();
        $data = $redis->hget($key,$hashKey);

        if (empty($data)) {
            $result = call_user_func_array($getter, $params);

            if ($result === false) {
                throw new Exception(ErrorCode::ERR_EMPTY_RESULT[0], '回调函数无返回！');
            }

            $redis->hset($key, $hashKey, serialize($result));
            $redis->expire($key, $timeout);
            return $result;
        } else {
            try {
                return unserialize($data);
            } catch (\Exception $e) {
                $redis->hDel($key,$hashKey);
            }

            if ($retry) {
                return self::getHashValue($key, $hashKey, $getter, $params, $timeout, false);
            }
        }

        throw new Exception(ErrorCode::ERR_EMPTY_RESULT[0], '回调函数无返回！');
    }



    /**
     * 通过keys批量删除key
     * @param string $pattern 不允许直接使用*
     * @return array 被删除的keys
     * @throws Exception
     */
    public static function clearByKeys($pattern)
    {
        return false;//此方法全面禁止使用
        $keys = array_unique(self::stringToArray($pattern));
        if (implode('', $keys) == '*') {
            throw new Exception(ErrorCode::ERR_PARAM[0], "不允许的key：{$pattern}");
        }

        $redis = self::getRedis();
        $allKeys = $redis->keys($pattern);

        foreach($allKeys as $key){
            $redis->del($key);
        }

        return $allKeys;
    }

    /**
     * 将字串转为数组
     * @param $string
     * @return array
     */
    private static function stringToArray($string)
    {
        $word = array();
        for ($i = 0; $i < mb_strlen($string); $i++) {
            $word[] = mb_substr($string, $i, 1);
        }

        return $word;
    }

    /**
     * 通过key获取，在原生的基础上将获取到的结果反序列化
     * @param $key
     * @return bool|mixed|string
     */
    public static function get($key)
    {
        $result = self::getRedis()
            ->get($key);

        if ($result) {
            return unserialize($result);
        } else {
            return $result;
        }
    }

    /**
     * 设置缓存，在原生的基础上将存入的值进行序列化
     * @param $key
     * @param $value
     * @param int $timeout
     * @return bool
     */
    public static function set($key, $value, $timeout = null)
    {
        $value = serialize($value);

        return self::getRedis()
            ->set($key, $value, $timeout);
    }

    /**
     * 删除缓存
     * @param $key
     * @return int
     */
    public static function del($key)
    {
        return self::getRedis()
            ->del($key);
    }

    /**
     * 获取redis实例
     * @return \Redis
     */
    public static function getRedis()
    {
        return \Yii::$app->get('redis');
    }

}