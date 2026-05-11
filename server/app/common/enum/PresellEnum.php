<?php

namespace app\common\enum;

class PresellEnum
{
    //
    const TYPE_ALL = 0;
    
    const TYPES = [
        self::TYPE_ALL,
    ];
    
    static function getTypeDesc($key = true): array|string
    {
        $data = [
            self::TYPE_ALL   => '全款预售',
        ];
        
        return $key === true ? $data : ($data[$key] ?? '');
    }
    
    //
    const SEND_TYPE_PAY_SUCCESS = 0;
    const SEND_TYPE_END = 1;
    
    const SEND_TYPES  = [
        self::SEND_TYPE_PAY_SUCCESS,
        self::SEND_TYPE_END,
    ];
    
    static function getSendTypeDesc($key = true): array|string
    {
        $data = [
            self::SEND_TYPE_PAY_SUCCESS     => '支付成功',
            self::SEND_TYPE_END             => '预售结束',
        ];
    
        return $key === true ? $data : ($data[$key] ?? '');
    }
    
    //
    const STATUS_WAIT   = 0;
    const STATUS_START  = 1;
    const STATUS_END    = 2;
    
    const STATUS_CAN_EDIT_ARR = [
        self::STATUS_WAIT,
        self::STATUS_START,
    ];
    const STATUS_GOODS_HAS = [
        self::STATUS_WAIT,
        self::STATUS_START,
    ];
    
    static function getStatusDesc($key = true): array|string
    {
        $data = [
            self::STATUS_WAIT   => '未开始',
            self::STATUS_START  => '进行中',
            self::STATUS_END    => '已结束',
        ];
        
        return $key === true ? $data : ($data[$key] ?? '');
    }
}