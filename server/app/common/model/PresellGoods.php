<?php

namespace app\common\model;

use think\model\concern\SoftDelete;

class PresellGoods extends BaseModel
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
    
    function getShowGoodsAttr($fieldValue, $data)
    {
        if ($data['min_price'] == $data['max_price']) {
            $presell_price  = "{$data['min_price']}";
        } else {
            $presell_price  = "{$data['min_price']}~{$data['max_price']}";
        }
        
        if ($this->content['min_price'] == $this->content['max_price']) {
            $sell_price     = "{$this->content['min_price']}";
        } else {
            $sell_price     = "{$this->content['min_price']}~{$this->content['max_price']}";
        }
        
        return [
            'presell_price'         => $presell_price,
            'presell_min_price'     => "{$data['min_price']}",
            'presell_max_price'     => "{$data['max_price']}",
            'sell_price'            => $sell_price,
            'sell_min_price'        => "{$this->content['min_price']}",
            'sell_max_price'        => "{$this->content['max_price']}",
            'name'                  => $this->content['name'] ?? '',
            'image'                 => $this->content['image'] ?? '',
        ];
    }
    
    /**
     * @notes 预售商品规格列表
     * @return \think\model\relation\HasMany
     * @author lbzy
     * @datetime 2024-04-24 14:19:51
     */
    function items()
    {
        return $this->hasMany(PresellGoodsItem::class, 'presell_goods_id', 'id');
    }
    
    function item()
    {
        return $this->hasMany(PresellGoodsItem::class, 'presell_goods_id', 'id');
    }
    
    /**
     * @notes 商品详情
     * @return \think\model\relation\HasOne
     * @author lbzy
     * @datetime 2024-04-24 14:19:39
     */
    function detail()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id');
    }
}