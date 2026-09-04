<?php
namespace app\adminapi\logic\order;

use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\logic\BaseLogic;
use app\common\model\Order;

class ErpOrderLogic extends BaseLogic
{
    public function detail($params)
    {
        $info = Order::withTrashed()->alias('o')
            ->join('user u', 'o.user_id = u.id')
            ->where('o.id', $params['id'])
            ->where('o.order_type', OrderEnum::ERP_ORDER)
            ->with(['order_goods' => function ($query) {
                $query->field('id,order_id,goods_id,goods_snap,goods_name,goods_price,goods_num,total_price,original_price')
                    ->append(['goods_image', 'spec_value_str']);
            }])
            ->field('o.id,o.order_status,o.sn,o.order_type,o.order_terminal,o.create_time,o.pay_status,o.pay_way,o.pay_time,confirm_take_time,u.id as user_id,
            u.sn as user_sn,u.nickname,o.address,o.user_remark,o.order_remarks,
            o.discount_amount,o.member_amount,o.change_price,o.express_price,o.order_amount,o.deduct_amount')
            ->append(['order_status_desc', 'order_type_desc', 'order_terminal_desc', 'pay_status_desc', 'pay_way_desc'])
            ->find();

        if (empty($info)) {
            self::$error = '订单不存在';
            return false;
        }

        $info = $info->toArray();

        $addr = $info['address'] ?? [];
        $info['contact'] = is_array($addr) ? ($addr['contact'] ?? '') : ($addr->contact ?? '');
        $info['mobile'] = is_array($addr) ? ($addr['mobile'] ?? '') : ($addr->mobile ?? '');

        foreach ($info['order_goods'] as $key => $goods) {
            $info['order_goods'][$key]['total_amount'] = round($goods['original_price'] * $goods['goods_num'], 2);
        }

        return $info;
    }
}
