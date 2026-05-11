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


use app\common\enum\IntegralOrderEnum;
use app\common\enum\PayEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\IntegralOrderRefundLogic;
use app\common\model\IntegralOrder;
use app\common\service\ConfigService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

/**
 * 关闭超时待付款积分订单
 * Class OrderClose
 * @package app\common\command
 */
class IntegralOrderClose extends Command
{

    protected function configure()
    {
        $this->setName('integral_order_close')
            ->setDescription('系统关闭超时未付款积分订单');
    }

    protected function execute(Input $input, Output $output)
    {
        $now = time();
        $ableClose = ConfigService::get('transaction', 'cancel_unpaid_orders');
        $cancelTime = ConfigService::get('transaction', 'cancel_unpaid_orders_times') * 60;

        if ($ableClose == YesNoEnum::NO) {
            return true;
        }

        $orders = IntegralOrder::field('id')
            ->whereRaw("create_time+$cancelTime < $now")
            ->where([
                'order_status' => IntegralOrderEnum::ORDER_STATUS_NO_PAID,
                'pay_status' => PayEnum::UNPAID,
            ])
            ->select()
            ->toArray();

        if (empty($orders)) {
            return true;
        }

        try{

            foreach ($orders as $order) {
                // 更新订单状态, 退回库存, 扣减销量
                IntegralOrderRefundLogic::cancelOrder($order['id']);

                // 退回已支付积分
                IntegralOrderRefundLogic::refundOrderIntegral($order['id']);
            }

        } catch(\Exception $e) {
            Log::write('积分订单自动关闭失败,失败原因:' . $e->getMessage());
        }
    }
}