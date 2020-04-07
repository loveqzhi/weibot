<?php

namespace BaseComponents\base;

/**
 * 记录SQL日志
 */
class SqlLog
{

    /** 日志 @var array */
    private static $queryLog = [];

    /** 开关 @var bool */
    private static $enable = false;

    /**
     * 追加日志
     * @param $sql
     */
    public static function append($sql)
    {
        // 判断debug模式，可在web/index.php中开启
        $debug = defined('YII_DEBUG') && YII_DEBUG;

        if ($debug || self::$enable) {
            self::$queryLog[] = $sql;
        }
    }

    /**
     * 获取日志
     * @return array
     */
    public static function getQueryLog()
    {
        return self::$queryLog;
    }

    /**
     * 开启SQL日志
     */
    public static function enableQueryLog()
    {
        self::$enable = true;
    }

}