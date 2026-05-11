<?php

namespace app\common\command;

use app\common\enum\DeliveryEnum;
use app\common\enum\IntegralGoodsEnum;
use app\common\enum\IntegralOrderEnum;
use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\model\IntegralOrder;
use app\common\model\Order;
use app\common\model\RechargeOrder;
use app\common\service\ConfigService;
use app\common\service\WechatMiniExpressSendSyncService;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class WechatMiniExpressSendSync extends Command
{
    protected function configure()
    {
        $this->setName('wechat_mini_express_send_sync')->setDescription('微信小程序发货同步');
    }
    
    protected function execute(Input $input, Output $output)
    {
        // 未开启发货同步
        if (! ConfigService::get('mini_program', 'express_send_sync', 1)) {
            return ;
        }
        // 订单
        static::order();
        // 积分订单
        static::integral_order();
        // 用户充值
        static::user_recharge();
    }
    
    private static function order()
    {
        // 快递
        $list = Order::where('delivery_type', DeliveryEnum::EXPRESS_DELIVERY)
            ->where('express_status', 1)
            ->where('pay_status', 1)
            ->where('pay_way', PayEnum::WECHAT_PAY)
            ->where('wechat_mini_express_sync', '<>',1)
            ->where('order_status', 'in', [ OrderEnum::STATUS_WAIT_RECEIVE, OrderEnum::STATUS_FINISH ])
            ->where('order_amount', '>',0)
            ->limit(60)
            ->order('id desc')
            ->select()->toArray();
        // 自提
        $list2 = Order::where('delivery_type', DeliveryEnum::SELF_DELIVERY)
            ->where('pay_status', 1)
            ->where('pay_way', PayEnum::WECHAT_PAY)
            ->where('wechat_mini_express_sync',  '<>',1)
            ->where('order_status', 'in', [ OrderEnum::STATUS_WAIT_DELIVERY, OrderEnum::STATUS_WAIT_RECEIVE, OrderEnum::STATUS_FINISH ])
            ->where('order_amount', '>',0)
            ->limit(20)
            ->order('id desc')
            ->select()->toArray();
        // 虚拟发货
        $list3 = Order::where('delivery_type', DeliveryEnum::DELIVERY_VIRTUAL)
            ->where('pay_status', 1)
            ->where('pay_way', PayEnum::WECHAT_PAY)
            ->where('wechat_mini_express_sync',  '<>',1)
            ->where('order_status', 'in', [ OrderEnum::STATUS_WAIT_RECEIVE, OrderEnum::STATUS_FINISH ])
            ->where('order_amount', '>',0)
            ->limit(20)
            ->order('id desc')
            ->select()->toArray();
    
        foreach ([ ...$list, ...$list2, ...$list3 ] as $item) {
            WechatMiniExpressSendSyncService::_sync_order($item);
        }
    }
    
    private static function integral_order()
    {
        $list = IntegralOrder::where('pay_status', 1)
            ->where('pay_way', PayEnum::WECHAT_PAY)
            ->where('wechat_mini_express_sync', 0)
            ->where('order_status', 'in', [ IntegralOrderEnum::ORDER_STATUS_GOODS, IntegralOrderEnum::ORDER_STATUS_COMPLETE ])
            ->where('order_amount', '>',0)
            ->limit(20)
            ->order('id desc')
            ->select()->toArray();
    
        foreach ($list as $item) {
            WechatMiniExpressSendSyncService::_sync_integral_order($item);
        }
    }
    
    private static function user_recharge()
    {
        $list = RechargeOrder::where('pay_status', 1)
            ->where('pay_way', PayEnum::WECHAT_PAY)
            ->where('wechat_mini_express_sync', 0)
            ->limit(60)
            ->order('id desc')
            ->cursor();
        
        foreach ($list as $item) {
            WechatMiniExpressSendSyncService::_sync_recharge($item->toArray());
        }
    }
    
}