<?php
namespace BaseComponents\base;

use Yii;
use yii\web\Cookie;

/**
 * 通用核心业务类
 *
 * @author coso
 */
class CoreHelper
{
    const FLAG_TRUE   = '1';
    const FLAG_FALSE  = '0';
    const DATE_FORMAT = 'Y-m-d H:i:s';
    const NOT_DELETED = 0;

    private static $_objects = array();

    public static function factory()
    {
        $args      = func_get_args();
        $className = array_shift($args);
        if (empty($className)) {
            $className = __CLASS__;
        }

        if (isset(self::$_objects[$className])) {
            return self::$_objects[$className];
        }

        if (class_exists($className) || interface_exists($className)) {
            self::$_objects[$className] = new $className($args);
            return self::$_objects[$className];
        }
        return false;
    }

    /**
     *
     * @return Redis the redis client
     */
    public static function getRedisClient()
    {
        return Yii::$app->redis;
    }

    /**
     *
     * @return MySQL the mysql client
     */
    public static function getDbClient()
    {
        return Yii::$app->db;
    }

    /**
     * 初始化返回
     *
     * @return StdClass
     */
    public static function initResult()
    {
        $result       = new \stdClass();
        $result->code = 0;
        $result->data = [];
        $result->msg  = 'success';
        return $result;
    }

    /**
     * 获取配置
     * @param $key
     * @param null $default
     * @param bool $static 是否存入静态变量中
     * @return null
     */
    public static function getOption($key, $default = null, $static = true)
    {
        static $option = null;
        $cacheKey = 'config:all_config';

        if (isset($option[$key]) && $option[$key] && $static) {
            return $option[$key];
        }

        if (!$static && isset($option[$key])) {
            unset($option[$key]);
        }

        //$data = Yii::$app->redis->get($cacheKey);
        $data = [];

        if (empty($data)) {
            /*$data = (new \yii\db\Query())->select('*')
                ->from('config')
                ->where('is_deleted=0')
                ->all(Yii::$app->config);*/
            $sql  = "select * from config where is_deleted = :deleted_flag";
            $data =  Yii::$app->db->createCommand($sql)->bindValues([':deleted_flag' => self::NOT_DELETED])->queryAll();

            if ($data === null) {
                if ($default !== null) {
                    $option[$key] = $default;
                }

                return $default;
            }

            foreach ($data as $value) {
                $option[$value['code']] = $value['value'];
            }

            //Yii::$app->redis->set($cacheKey, json_encode($option));
        } else {
            $option = json_decode($data, true);
        }

        // 表中没有记录则返回默认值
        if (!isset($option[$key])) {
            $option[$key] = $default;

            return $default;
        }

        return $option[$key];
    }

    /**
     * 获取app_config配置
     */
    public static function getAppOption($key, $default = null)
    {
        static $appOption = null;
        $cacheKey      = 'config:app_config';
        if (isset($appOption[$key]) && $appOption[$key]) {
            return $appOption[$key];
        }
        $data = Yii::$app->redis->get($cacheKey);
        if (empty($data)) {
            /*$data = (new \yii\db\Query())->select('*')
                ->from('app_config')
                ->where('is_deleted=0')
                ->all(Yii::$app->config);*/

            $sql  = "select * from app_config where is_deleted = :deleted_flag";
            $data =  Yii::$app->config->createCommand($sql)->bindValues([':deleted_flag' => self::NOT_DELETED])->queryAll();

            if ($data === null) {
                if ($default !== null) {
                    $appOption[$key] = $default;
                }
                return $default;
            }
            foreach ($data as $value) {
                $appOption[$value['code']] = $value['value'];
            }
            Yii::$app->redis->set($cacheKey, json_encode($appOption));
        } else {
            $appOption = json_decode($data, true);
        }
        // 表中没有记录则返回默认值
        if (!isset($appOption[$key])) {
            $appOption[$key] = $default;
            return $default;
        }
        return $appOption[$key];
    }

    /**
     * 检查是否为手机号码
     * 130-139, 145,147,150-159(排除154), 170, 180-189,
     *
     * @param string $value
     * @return boolean
     */
    public static function isMobile($value)
    {
        // return (preg_match ( '/^((\+86)|(86))?13[0-9]{9}|15[0|1|2|3|5|6|7|8|9]\d{8}|18[0|5|6|7|8|9]\d{8}$/', $value )) ? true : false;
        $patter = '/^1[34578]\d{9}$/';
        return (preg_match($patter, $value)) ? true : false;
    }

    /**
     * 校验车牌号 兼容最后中文字符
     * @param $value
     * @return bool
     * @throws Exception
     */
    public static function isCarNum($value)
    {
        if (!preg_match('/^[\x80-\xff]+[a-zA-Z][0-9a-zA-Z]{4}([0-9a-zA-Z]{1,2}|[\x80-\xff]+|[0-9a-zA-Z][\x80-\xff]+)$/', $value)) {
            throw new Exception(ErrorCode::PLATE_NUMBER_ERROR);
        }

        return true;
    }

    /**
     * 判读是否是windows平台
     *
     * @return boolean
     */
    public static function isWin()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return true;
        }

        return false;
    }

    public static function getClientIp()
    {
        global $HTTP_SERVER_VARS;

        if (!empty($_SERVER['HTTP_CDN_SRC_IP'])) {
            return $_SERVER['HTTP_CDN_SRC_IP'];
        }

        if (isset($HTTP_SERVER_VARS)) {
            if (isset($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])) {
                $realip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
            } elseif (isset($HTTP_SERVER_VARS["HTTP_CLIENT_IP"])) {
                $realip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
            } else {
                $realip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        $realip = explode(',', $realip);
        return $realip[0];
    }


    /**
     * 判断是否微信浏览器
     * 用于联登判断，切勿随意改动。150311
     *
     * @return boolean
     */
    public static function isWenXinBrowser()
    {
        $useragent = strtolower($_SERVER["HTTP_USER_AGENT"]);
        $isWenxin  = strripos($useragent, 'micromessenger');
        if ($isWenxin) {
            return true;
        } else {
            return false;
        }
    }

    public static function genRandChar($length)
    {
        $str    = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max    = strlen($strPol) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[mt_rand(0, $max)]; // rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }

    /**
     * 生成16进制随机字符串
     */
    public static function genHexRandChar($length)
    {
        $str    = null;
        $strPol = "0123456789abcdef";
        $max    = strlen($strPol) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[mt_rand(0, $max)]; // rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }


    public static function getCookies($key, $prefix = false)
    {
        if (empty($key)) {
            return false;
        }
        $value = false;
        /* 二维 */
        if (preg_match('/\[.*?\]/', $key)) {
            $posL   = strrpos($key, '[');
            $posR   = strrpos($key, ']');
            $oneDim = substr($key, 0, $posL);
            $twoDim = substr($key, $posL + 1, $posR - $posL - 1);

            $value = !empty($_COOKIE[$oneDim][$twoDim]) ? $_COOKIE[$oneDim][$twoDim] : null;
        } else {
            if ($prefix && stristr($key, COOKIE_PREFIX) === false) {
                $key = COOKIE_PREFIX . $key;
            }
            $value = empty($_COOKIE[$key]) ? null : $_COOKIE[$key];
        }

        return $value;
    }

    /**
     *
     * @name 记录COOKIE
     * @param int $expire
     */
    public static function setCookies($key = '', $value = '', $expire = 0, $prefix = false)
    {
        if (empty($key)) {
            return false;
        }
        $now = CURRENT_TIMESTAMP;
        if ($expire == 0) {
            $time = $now + 3600 * 24 * COOKIE_EXPIRE;
        } else
        if ($expire > 0) {
            $time = $now + $expire;
        } else {
            $time = 0;
        }
        if ($prefix && !preg_match('/\[.*?\]/', $key) && stristr($key, COOKIE_PREFIX) === false) {
            $key = COOKIE_PREFIX . $key;
        }
        if ($key && $value) {
            return setcookie($key, $value, $time, COOKIE_PATH, COOKIE_DOMAIN);
        } else
        if ($key) {
            return setcookie($key, '', $now - 86400, COOKIE_PATH, COOKIE_DOMAIN);
        }
        return false;
    }

    public static function clearCookies()
    {
        $cookies = Yii::$app->getRequest()->getCookies();
        foreach ($cookies as $cookie) {
            // 部分Cookie无需过滤
            $filter = ['clientVersion', 'platform'];
            if (in_array($cookie->name, $filter)) {
                continue;
            }

            $cookie = new Cookie([
                'name'   => $cookie->name,
                'value'  => '',
                'expire' => 0,
                'domain' => COOKIE_DOMAIN,
            ]);
            \Yii::$app->getResponse()
                ->getCookies()
                ->add($cookie);
        }
    }

    public static function write($msg,$header = '')
    {
        if (is_array($msg) || is_object($msg)) {
            $msg = json_encode($msg);
        }

        $msg = date("Y-m-d H:i:s") . '：|'.$header.'|' . $msg;


        $path = '/home/wwwlogs/' . $_SERVER['SERVER_NAME'] . '/';
        if (!is_dir($path)) {
            mkdir($path);
        }
//         $time     = date('Ymd');
//         $fileName = $path . "{$time}.txt";
        $fileName = $path . "php.log";
        file_put_contents($fileName, PHP_EOL . $msg, FILE_APPEND | LOCK_EX);
    }

    /**
     * 注意：1,包含敏感信息的日志请勿使用该函数进行记录
     *       2,用command形式运行的 $_SERVER['SERVER_NAME'] 为“commands”
     */
    public static function openLog( $data, $path=array(), $file=NULL )
    {
        $basePath = '/home/wwwlogs/' . $_SERVER['SERVER_NAME'] . '/openlog';
        if(count($path)>0) $basePath.= '/'. implode('/', $path);
        
        if (!is_dir($basePath)) {
            mkdir($basePath, 0777, TRUE);
        }
        if(!$file) $fileName= $basePath . '/'. date("Y-m-d_H"). ".txt";
        else $fileName= $file;
    
        if (is_array($data) || is_object($data)) {
            $content = json_encode($data);
        } else {
            $content = $data;
        }
    
        $ip= self::getClientIp();

        $content = date("Y-m-d H:i:s") . ' |'. $ip. PHP_EOL . $content;
        file_put_contents($fileName, $content . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    

    /**
     * 计算两组经纬度坐标 之间的距离
     * params ：lat1 纬度1； lng1 经度1； lat2 纬度2； lng2 经度2； len_type （1:m or 2:km);
     * return m or km
     */

    public static function GetDistance($lat1, $lng1, $lat2, $lng2, $len_type = 1, $decimal = 2)
    {
        defined('EARTH_RADIUS') || define('EARTH_RADIUS', 6378.137); // 地球半径
        defined('PI') || define('PI', 3.1415926);
        $radLat1 = $lat1 * PI / 180.0;
        $radLat2 = $lat2 * PI / 180.0;
        $a       = $radLat1 - $radLat2;
        $b       = ($lng1 * PI / 180.0) - ($lng2 * PI / 180.0);
        $s       = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $s       = $s * EARTH_RADIUS;
        $s       = round($s * 1000);
        if ($len_type > 1) {
            $s /= 1000;
        }
        return round($s, $decimal);
    }

    /**
     * 生成订单id
     */
    public static function makeOrderId()
    {
        list($t1, $t2) = explode(' ', microtime());
        $mtime = (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000) - (time() . '000');
        $mtime = $mtime . rand(0, 9);
        $mtime = substr(sprintf('%04d', $mtime), -4);

        return date('ymdHis') . $mtime;
    }

}
