<?php

namespace app\adminapi\lists\marketing;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\PresellEnum;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsExtendInterface;
use app\common\model\Presell;

/**
 * @notes notes
 * author lbzy
 * @datetime 2024-04-24 14:09:18
 * @class PresellLists
 * @package app\adminapi\lists\marketing
 */
class PresellLists extends BaseAdminDataLists implements ListsExtendInterface, ListsExcelInterface
{
    
    function setExcelFields(): array
    {
        return [
            'sn'            => '预售编号',
            'name'          => '预售名称',
            'start_time'    => '开始时间',
            'end_time'      => '结束时间',
            'create_time'   => '创建时间',
        ];
    }
    
    public function setFileName(): string
    {
        return '预售列表';
    }
    
    /**
     * @inheritDoc
     */
    public function lists(): array
    {
        $withSearch = [ 'goods', 'name', 'start_time', 'end_time', 'status' ];
        
        $lists = Presell::alias('p')
            ->withSearch($withSearch, $this->params)
            ->append([ 'type_text', 'status_text', 'send_type_text' ])
            ->order('p.id desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();
        
        return $lists;
    }
    
    /**
     * @inheritDoc
     */
    public function count(): int
    {
        $withSearch = [ 'goods', 'name', 'start_time', 'end_time', 'status' ];
        
        return Presell::alias('p')
            ->withSearch($withSearch, $this->params)
            ->order('p.id desc')
            ->count();
    }
    
    public function extend()
    {
        $withSearch = [ 'goods', 'name', 'start_time', 'end_time' ];
        
        $lists = Presell::alias('p')
            ->withSearch($withSearch, $this->params)
            ->field([ 'status,count(*) as count' ])
            ->group('status')
            ->select()->toArray();
        
        $lists = array_column($lists, 'count', 'status');
        
        return [
            'all'   => array_sum($lists),
            'wait'  => $lists[PresellEnum::STATUS_WAIT] ?? 0,
            'start' => $lists[PresellEnum::STATUS_START] ?? 0,
            'end'   => $lists[PresellEnum::STATUS_END] ?? 0,
        ];
    }
}