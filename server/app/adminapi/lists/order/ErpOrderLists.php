<?php
namespace app\adminapi\lists\order;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\OrderEnum;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsExtendInterface;
use app\common\model\Order;

class ErpOrderLists extends BaseAdminDataLists implements ListsExtendInterface, ListsExcelInterface
{
    public function lists(): array
    {
        try {
            return Order::withTrashed()->alias('o')
                ->join('user u', 'o.user_id = u.id')
                ->field('o.id,o.sn,o.order_type,o.order_amount,o.deduct_amount,o.address,o.pay_status,o.order_status,o.create_time,u.id as user_id,u.nickname,u.sn as user_sn,u.avatar,o.delivery_type,o.pay_way,o.user_remark,o.order_remarks')
                ->where('o.order_type', OrderEnum::ERP_ORDER)
                ->order('o.id', 'desc')
                ->append(['order_type_desc', 'pay_status_desc', 'order_status_desc', 'delivery_type_desc'])
                ->select()
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function count(): int
    {
        try {
            return Order::withTrashed()->where('order_type', OrderEnum::ERP_ORDER)->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function extend(): array
    {
        return [];
    }

    public function setExcelFields(): array
    {
        return [
            ['field' => 'sn', 'name' => '订单编号'],
            ['field' => 'user_sn', 'name' => '用户编号'],
            ['field' => 'nickname', 'name' => '用户昵称'],
            ['field' => 'order_amount', 'name' => '订单金额'],
            ['field' => 'deduct_amount', 'name' => '活动抵扣'],
            ['field' => 'pay_amount', 'name' => '实付金额'],
            ['field' => 'pay_status_desc', 'name' => '支付状态'],
            ['field' => 'create_time', 'name' => '下单时间'],
            ['field' => 'user_remark', 'name' => '备注'],
        ];
    }

    public function setFileName(): string
    {
        return 'ERP订单列表';
    }
}
