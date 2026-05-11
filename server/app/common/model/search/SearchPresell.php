<?php

namespace app\common\model\search;

use app\common\model\Goods;
use think\facade\Validate;

trait SearchPresell
{
    /**
     * @notes 搜索状态
     * @param $query
     * @param $value
     * @param $data
     * @return void
     * @author lbzy
     * @datetime 2024-04-30 11:30:43
     */
    function searchStatusAttr($query, $value, $data)
    {
        Validate::must($value) && $query->where('p.status', $value);
    }
    
    /**
     * @notes 搜索商品
     * @param $query
     * @param $value
     * @param $data
     * @return false|void
     * @author lbzy
     * @datetime 2024-04-24 14:32:10
     */
    function searchGoodsAttr($query, $value, $data)
    {
        if(empty($value)) {
            return false;
        }
        
        $goods_ids = Goods::where('name|code', 'like', "%{$value}%")->column('id');
        
        $query->where('p.id', 'in', self::where('goods_id', 'in', $goods_ids)->column('presell_id'));
    }
    
    /**
     * @notes 搜索名称
     * @param $query
     * @param $value
     * @param $data
     * @return void
     * @author lbzy
     * @datetime 2024-04-24 14:36:30
     */
    function searchNameAttr($query, $value, $data)
    {
        Validate::must($value) && $query->where('p.name', 'like', "%{$value}%");
    }
    
    /**
     * @notes 搜索开始时间
     * @param $query
     * @param $value
     * @param $data
     * @return void
     * @author lbzy
     * @datetime 2024-04-24 14:39:08
     */
    function searchStartTimeAttr($query, $value, $data)
    {
        Validate::must($value) && $query->where('p.start_time', '>=', strtotime($value));
    }
    
    /**
     * @notes 搜索结束时间
     * @param $query
     * @param $value
     * @param $data
     * @return void
     * @author lbzy
     * @datetime 2024-04-24 14:39:18
     */
    function searchEndTimeAttr($query, $value, $data)
    {
        Validate::must($value) && $query->where('p.end_time', '<=', strtotime($value));
    }
}