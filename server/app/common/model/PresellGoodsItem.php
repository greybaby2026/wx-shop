<?php

namespace app\common\model;

use think\model\concern\SoftDelete;

class PresellGoodsItem extends BaseModel
{
    use SoftDelete;
    protected $autoWriteTimestamp = true;
    protected $defaultSoftDelete = 0;
    
    function getContentAttr($fieldValue, $data)
    {
        return (array) json_decode($fieldValue, true);
    }
    
    function setContentAttr($fieldValue, $data)
    {
        return json_encode($fieldValue, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * @notes 商品规格详情
     * @return \think\model\relation\HasOne
     * @author lbzy
     * @datetime 2024-04-24 14:19:39
     */
    function detail()
    {
        return $this->hasOne(GoodsItem::class, 'id', 'item_id');
    }
}