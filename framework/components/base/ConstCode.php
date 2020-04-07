<?php
namespace BaseComponents\base;

/**
 * 常量定义管理
 *
 */
class ConstCode
{
    /**
     * 订单状态定义
     */
    const ORDER_STATUS_TO_CONFIRM = [
        0,
        '已付款'
    ];

    
    public static function getConstDesc($key) {
        $oClass = new \ReflectionClass(static::class);
        $constAry = $oClass->getConstants();

        $desc = [];
        foreach ( $constAry as $const => $v ) {
            if ( strpos($const, $key ) === 0 ) {
                $desc[$v[0]] = $v[1];
            }
        }

        return $desc;
    }

    public static function getDesc($key) {
        $oClass = new \ReflectionClass(static::class);
        $constAry = $oClass->getConstants();
        foreach ( $constAry as $const => $v ) {
            if ( $v[0] === $key) {
               return $v[1];
            }
        }

        return '';
    }
}
