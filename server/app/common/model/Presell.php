<?php

namespace app\common\model;

use app\common\enum\PresellEnum;
use app\common\model\search\SearchPresell;
use think\model\concern\SoftDelete;

class Presell extends BaseModel
{
    use SoftDelete;
    use SearchPresell;
    protected $autoWriteTimestamp = true;
    protected $defaultSoftDelete = 0;
    
    protected $type = [
        'start_time'    => 'timestamp',
        'end_time'      => 'timestamp',
    ];
    
    // 多条
    function goods()
    {
        return $this->hasMany(PresellGoods::class, 'presell_id', 'id');
    }
    
    // 单条
    function goodsDetail()
    {
        return $this->hasOne(PresellGoods::class, 'presell_id', 'id');
    }
    
    function goodsItems()
    {
        return $this->hasMany(PresellGoodsItem::class, 'presell_id', 'id');
    }
    
    /**
     * @notes type_text
     * @param $fieldValue
     * @param $data
     * @return string|string[]
     * @author lbzy
     * @datetime 2024-04-24 14:43:51
     */
    function getTypeTextAttr($fieldValue, $data)
    {
        return PresellEnum::getTypeDesc($data['type']);
    }
    
    /**
     * @notes status_text
     * @param $fieldValue
     * @param $data
     * @return string|string[]
     * @author lbzy
     * @datetime 2024-04-24 14:44:02
     */
    function getStatusTextAttr($fieldValue, $data)
    {
        return PresellEnum::getStatusDesc($data['status']);
    }
    
    /**
     * @notes send_type_text
     * @param $fieldValue
     * @param $data
     * @return array|string
     * @author lbzy
     * @datetime 2024-04-24 15:10:06
     */
    function getSendTypeTextAttr($fieldValue, $data)
    {
        return PresellEnum::getSendTypeDesc($data['send_type']);
    }
    
    /**
     * @notes end_time_remain 结束倒计时
     * @return mixed
     * @author lbzy
     * @datetime 2024-04-30 14:23:59
     */
    function getEndTimeRemainAttr($fieldValue, $data)
    {
        if ($data['status'] != PresellEnum::STATUS_START) {
            return null;
        }
        
        return $this->getOrigin('end_time') - time();
    }
    
    /**
     * @notes goods_num 商品数量
     * @return int
     * @author lbzy
     * @datetime 2024-06-14 18:44:32
     */
    function getGoodsNumAttr($fieldValue, $data)
    {
        return PresellGoods::where('presell_id', $data['id'])->count();
    }
}