<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
use app\common\command\Presell;
use app\common\command\WechatMiniExpressSendSync;

return [
    // 指令定义
    'commands' => [
        // 分销结算
        'distribution_settlement' => 'app\common\command\DistributionSettlement',
        // 过期优惠券
        'coupon_end' => 'app\common\command\CouponEnd',
        // 拼团结束
        'team_end' => 'app\common\command\TeamEnd',
        // 订单自动确认收货
        'order_confirm' => 'app\common\command\OrderConfirm',
        // 自动关闭超时未付款订单
        'order_close' => 'app\common\command\OrderClose',
        // 自动关闭超时未付款积分订单
        'integral_order_close' => 'app\common\command\IntegralOrderClose',
        // 售后退款查询
        'after_sale_refund' => 'app\common\command\AfterSaleRefund',
        // 结束过期的砍价活动
        'bargain_close' => 'app\common\command\BargainClose',
        // 结束过期的秒杀活动
        'seckill_end' => 'app\common\command\SeckillEnd',
        // 修改超级管理员密码
        'password' => 'app\common\command\Password',
        // 结束过期的幸运抽奖
        'lucky_draw_end' => 'app\common\command\LuckyDrawEnd',
        // 定时任务
        'crontab' => 'app\common\command\Crontab',
        // 结算消费赠送积分
        'award_integral' => 'app\common\command\AwardIntegral',
        // 商家转账到零钱查询
        'wechat_merchant_transfer' => 'app\common\command\WechatMerchantTransfer',
        // 结束过期的包邮活动
        'free_shipping_end' => 'app\common\command\FreeShippingEnd',
        // 优惠券结束领取
        'coupon_receive_end' => 'app\common\command\CouponReceiveEnd',
        
        // 微信小程序 发货信息同步
        'wechat_mini_express_send_sync' => WechatMiniExpressSendSync::class,
        
        'presell'                       => Presell::class,

        'ai-test' => 'app\common\command\AiTest',
        'ai-debug' => 'app\common\command\AiDebug',
        'wecom-test' => 'app\common\command\WecomTest',
        'wecom-debug' => 'app\common\command\WecomDebug',
        'kefu:daily-report' => 'app\common\command\DailyReport',
    ],
];
