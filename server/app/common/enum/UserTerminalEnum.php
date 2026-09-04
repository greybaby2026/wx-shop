<?php

namespace app\common\enum;

class UserTerminalEnum
{
    const WECHAT_MMP = 1;
    const WECHAT_OA  = 2;
    const H5         = 3;
    const PC         = 4;
    const IOS        = 5;
    const ANDROID    = 6;
    const TOUTIAO    = 7;
    const ERP        = 8;

    const ALL_TERMINAL = [
        self::WECHAT_MMP, self::WECHAT_OA, self::H5,
        self::PC, self::IOS, self::ANDROID, self::TOUTIAO,
        self::ERP,
    ];

    public static function getTermInalDesc($from = true)
    {
        $desc = [
            self::WECHAT_MMP => '微信小程序',
            self::WECHAT_OA  => '微信公众号',
            self::H5         => '手机H5',
            self::PC         => '电脑PC',
            self::IOS        => '苹果APP',
            self::ANDROID    => '安卓APP',
            self::TOUTIAO    => '头条小程序',
            self::ERP        => 'ERP门店',
        ];
        if(true === $from) return $desc;
        return $desc[$from] ?? '';
    }

    public static function trueerminalEnumByScene($scene)
    {
        $desc = [
            self::WECHAT_MMP => 1, self::WECHAT_OA => 2,
            self::H5 => 3, self::PC => 4,
            self::IOS => 5, self::ANDROID => 5,
            self::TOUTIAO => 7, self::ERP => 8,
        ];
        return $desc[$scene] ?? 0;
    }
}
