<?php

namespace BaseComponents\base;

/**
 * 日历助手
 * @author bo.lin
 */
class Calendar
{

    /**
     * 日期验证格式
     * @param $dateTime
     * @param string $format
     * @return bool
     */
    public static function checkFormat($dateTime, $format = 'Y-m-d H:i:s')
    {
        return date($format, strtotime($dateTime)) == $dateTime;
    }

    /**
     * 日期比较
     * @param $dateTime1
     * @param $dateTime2
     * @return int $dateTime1 > $dateTime2 返回1；$dateTime1 = $dateTime2 返回0；$dateTime1 < $dateTime2 返回-1
     */
    public static function compare($dateTime1, $dateTime2)
    {
        $ts1 = strtotime($dateTime1);
        $ts2 = strtotime($dateTime2);

        if ($ts1 > $ts2) {
            return 1;
        } elseif ($ts1 < $ts2) {
            return -1;
        } else {
            return 0;
        }
    }
}