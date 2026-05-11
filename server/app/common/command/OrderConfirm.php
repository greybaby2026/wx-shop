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

namespace app\common\command;


use app\common\enum\DeliveryEnum;
use app\common\enum\OrderEnum;
use app\common\enum\OrderLogEnum;
use app\common\enum\PayEnum;
use app\common\enum\YesNoEnum;
use app\common\model\Order;
use app\common\model\OrderLog;
use app\common\service\ConfigService;
use app\shopapi\logic\Order\OrderLogic;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\facade\Log;

/**
 * 订单自动完成
 * Class OrderFinish
 * @package app\common\command
 */
class OrderConfirm extends Command
{

    protected function configure()
    {
        $this->setName('order_confirm')
            ->setDescription('系统自动确认收货');
    }

    protected function execute(Input $input, Output $output)
    {
        $now = time();
        $ableAuto = ConfigService::get('transaction', 'automatically_confirm_receipt');
        $confirmTime = ConfigService::get('transaction', 'automatically_confirm_receipt_days') * 24 * 60 * 60;

        if ($ableAuto == YesNoEnum::NO) {
            return true;
        }

        $orders = Order::with([ 'after_sale_ing' ])
            ->where([
            'order_status' => OrderEnum::STATUS_WAIT_RECEIVE,
            'pay_status' => PayEnum::ISPAID
            ])
            ->whereRaw("express_time+$confirmTime < $now")
            ->where('delivery_type','<>',DeliveryEnum::SELF_DELIVERY)
            ->select();

        try {
            foreach ($orders as $order) {
                // 是否售后中
                if (isset($order['after_sale_ing']['id'])) {
                    continue;
                }
                //更新订单状态
                Order::update([
                    'order_status' => OrderEnum::STATUS_FINISH,
                    'confirm_take_time' => time(),
                    'after_sale_deadline' => OrderLogic::getAfterSaleDeadline(), //售后截止时间
                ], ['id' => $order['id']]);

                //订单日志
                (new OrderLog())->record([
                    'type' => OrderLogEnum::TYPE_SYSTEM,
                    'channel' => OrderLogEnum::SYSTEM_CONFIRM_ORDER,
                    'order_id' => $order['id']
                ]);
            }
        } catch (\Exception $e) {
            Log::write('订单自动确认失败,失败原因:' . $e->getMessage());
        }
    }
}