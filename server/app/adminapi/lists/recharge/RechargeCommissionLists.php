<?php

namespace app\adminapi\lists\recharge;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\RechargeCommission;
use app\common\service\FileService;

class RechargeCommissionLists extends BaseAdminDataLists implements ListsSearchInterface, ListsExcelInterface
{
    public function setSearch(): array
    {
        return [
            '=' => ['rc.level', 'rc.status'],
            'between_time' => 'rc.create_time'
        ];
    }

    public function attachSearch()
    {
        if (isset($this->params['keyword']) && !empty($this->params['keyword'])) {
            $this->searchWhere[] = ['fu.sn|fu.nickname|fu.mobile', 'like', '%' . $this->params['keyword'] . '%'];
        }
        if (isset($this->params['dkeyword']) && !empty($this->params['dkeyword'])) {
            $this->searchWhere[] = ['u.sn|u.nickname|u.mobile', 'like', '%' . $this->params['dkeyword'] . '%'];
        }
    }

    public function setFileName(): string
    {
        return '充值佣金表';
    }

    public function setExcelFields(): array
    {
        return [
            'sn' => '佣金编号',
            'recharge_order_sn' => '充值订单编号',
            'from_user_nickname' => '充值用户',
            'user_nickname' => '获得佣金用户',
            'level_desc' => '佣金层级',
            'ratio' => '佣金比例(%)',
            'recharge_amount' => '充值金额',
            'earnings' => '佣金金额',
            'status_desc' => '状态',
            'settle_time' => '结算时间',
        ];
    }

    public function lists(): array
    {
        $this->attachSearch();

        $field = 'rc.id, rc.sn, rc.recharge_order_sn, rc.level, rc.level as level_desc, rc.ratio, rc.recharge_amount, rc.earnings, rc.status, rc.status as status_desc, rc.settle_time, rc.create_time';
        $field .= ',u.nickname as user_nickname, u.avatar as user_avatar, u.sn as user_sn, u.mobile as user_mobile';
        $field .= ',fu.nickname as from_user_nickname, fu.avatar as from_user_avatar, fu.sn as from_user_sn, fu.mobile as from_user_mobile';

        $lists = RechargeCommission::alias('rc')
            ->leftJoin('user u', 'u.id = rc.user_id')
            ->leftJoin('user fu', 'fu.id = rc.from_user_id')
            ->field($field)
            ->where($this->searchWhere)
            ->order('rc.id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['user_avatar'] = FileService::getFileUrl($item['user_avatar']);
            $item['from_user_avatar'] = FileService::getFileUrl($item['from_user_avatar']);
            $item['settle_time'] = $item['settle_time'] ? date('Y-m-d H:i:s', intval($item['settle_time'])) : '';
            $item['create_time'] = is_numeric($item['create_time']) ? date('Y-m-d H:i:s', intval($item['create_time'])) : (string)$item['create_time'];
        }

        return $lists;
    }

    public function count(): int
    {
        $this->attachSearch();

        $count = RechargeCommission::alias('rc')
            ->leftJoin('user u', 'u.id = rc.user_id')
            ->leftJoin('user fu', 'fu.id = rc.from_user_id')
            ->where($this->searchWhere)
            ->count();

        return $count;
    }
}
