<?php

namespace app\adminapi\lists\goods;

use app\common\lists\BaseDataLists;
use app\common\model\GoodsDeliveryTemplate;

class GoodsDeliveryTemplateLists extends BaseDataLists
{
    
    /**
     * @inheritDoc
     */
    public function lists(): array
    {
        return GoodsDeliveryTemplate::where($this->searchWhere)->order('id desc')->select()->toArray();
    }
    
    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return GoodsDeliveryTemplate::where($this->searchWhere)->count();
    }
}