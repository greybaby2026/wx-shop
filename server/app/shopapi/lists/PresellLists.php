<?php

namespace app\shopapi\lists;

use app\common\enum\PresellEnum;
use app\common\lists\BaseDataLists;
use app\common\model\PresellGoods;

class PresellLists extends BaseDataLists
{
    
    
    /**
     * @inheritDoc
     */
    public function lists(): array
    {
        $field = [
            'pg.id', 'pg.goods_id', 'pg.presell_id', 'pg.click', 'pg.virtual_click', 'pg.virtual_sale',
            'pg.max_price', 'pg.min_price',
            'content',
        ];
        
        $lists = PresellGoods::alias('pg')
            ->join('presell p', 'p.id=pg.presell_id')
            ->field($field)
            ->with([ 'items' ])
            ->append([ 'show_goods' ])
            ->hidden([ 'content' ])
            ->where('p.status', PresellEnum::STATUS_START)
            ->where('p.start_time', '<=', time())
            ->where('p.end_time', '>=', time())
            ->order('pg.min_price asc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()->toArray();
        
        foreach ($lists as &$info) {
            $info['sale_nums'] = array_sum(array_column($info['items'], 'sale_nums'));
            unset($info['items']);
        }
        
        return $lists;
    }
    
    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return PresellGoods::alias('pg')
            ->join('presell p', 'p.id=pg.presell_id')
            ->field('pg.id')
            ->where('p.status', PresellEnum::STATUS_START)
            ->where('p.start_time', '<=', time())
            ->where('p.end_time', '>=', time())
            ->count();
    }
}