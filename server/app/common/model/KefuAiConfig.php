<?php

namespace app\common\model;

use think\Model;

class KefuAiConfig extends Model
{
    protected $name = 'kefu_ai_config';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static function getConfigValue($type, $default = '')
    {
        $config = self::where('type', $type)->find();
        return $config ? $config->value : $default;
    }

    public static function setConfigValue($type, $value)
    {
        $config = self::where('type', $type)->find();
        if ($config) {
            $config->value = $value;
            $config->save();
        } else {
            self::create(['type' => $type, 'value' => $value]);
        }
    }
}