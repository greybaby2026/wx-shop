<?php

namespace app\common\model;

use think\model\concern\SoftDelete;

class GoodsDeliveryTemplate extends BaseModel
{
    use SoftDelete;
    
    protected $deleteTime = 'delete_time';
    
    function setContentAttr($fieldValue, $data)
    {
        return (string) $fieldValue;
    }
    
    function getContent1Attr($fieldValue, $data)
    {
        return $fieldValue ? json_decode($fieldValue, true) : [];
    }
    
    function setContent1Attr($fieldValue, $data)
    {
        return is_array($fieldValue) ? json_encode($fieldValue, JSON_UNESCAPED_UNICODE) : ((string) $fieldValue);
    }
}