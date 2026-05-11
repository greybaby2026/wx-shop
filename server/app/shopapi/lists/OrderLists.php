<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------

namespace app\shopapi\lists;

use app\common\enum\DeliveryEnum;
use app\common\enum\OrderEnum;
use app\common\enum\TeamEnum;
use app\common\logic\CommonPresellLogic;
use app\common\model\Order;


class OrderLists extends BaseShopDataLists
{

    /**
     * @notes 列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2021/7/23 18:55
     */
    public function lists(): array
    {
        $lists = Order::withSearch(['order_type', 'user_id'], [
                'order_type' => $this->params['type'],
                'user_id' => $this->userId
            ])
            ->with(['order_goods' => function ($query) {
                $query->field('goods_id,order_id,goods_snap,goods_name,goods_price,goods_num,is_comment,original_price')
                    ->append(['goods_image', 'spec_value_str'])
                    ->hidden(['goods_snap']);
            }])
            ->field(['id', 'sn', 'order_type', 'order_status', 'total_num', 'order_amount', 'delivery_type', 'is_team_success', 'pay_way', 'pay_status', 'pay_time', 'express_status','delivery_content', 'delivery_content1', 'delivery_content_type', 'create_time', 'presell_id', 'transaction_id' ])
            ->append(['btn','cancel_unpaid_orders_time'])
            ->order(['id' => 'desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()->toArray();

        foreach ($lists as &$list){
            //查看提货码按钮
            $list['btn']['pickup_btn'] = ($list['order_status'] == OrderEnum::STATUS_WAIT_DELIVERY && $list['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? 1 : 0;
            //订单状态描述
            $list['order_status_desc'] = ($list['order_status'] == OrderEnum::STATUS_WAIT_DELIVERY && $list['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? '待取货' : OrderEnum::getOrderStatusDesc($list['order_status']);
            if ($list['order_type'] == OrderEnum::TEAM_ORDER && $list['is_team_success'] != TeamEnum::TEAM_FOUND_SUCCESS){
                $list['btn']['pickup_btn'] = 0;
                $list['order_status_desc'] = ($list['order_status'] == OrderEnum::STATUS_WAIT_DELIVERY) ? TeamEnum::getStatusDesc($list['is_team_success']) : OrderEnum::getOrderStatusDesc($list['order_status']);
            }



            //订单类型
            $list['order_type_desc'] = ($list['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? '自提订单' : OrderEnum::getOrderTypeDesc($list['order_type']);
            
            // 预售信息
            if ($list['order_type'] == OrderEnum::PRESELL_ORDER) {
                $list['presell'] = CommonPresellLogic::orderInfo($list);
            }
        }

        return $lists;
    }


    /**
     * @notes 数量
     * @return int
     * @author 段誉
     * @date 2021/7/23 18:55
     */
    public function count(): int
    {
        return Order::withSearch(['order_type', 'user_id'], [
            'order_type' => $this->params['type'],
            'user_id' => $this->userId
        ])->count();
    }
}