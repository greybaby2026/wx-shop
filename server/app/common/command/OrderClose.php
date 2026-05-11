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


use app\common\enum\OrderEnum;
use app\common\enum\OrderLogEnum;
use app\common\enum\PayEnum;
use app\common\enum\YesNoEnum;
use app\common\model\Goods;
use app\common\model\GoodsItem;
use app\common\model\Order;
use app\common\model\OrderLog;
use app\common\service\after_sale\AfterSaleService;
use app\common\service\ConfigService;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;
use think\facade\Log;

/**
 * 关闭超时待付款订单
 * Class OrderClose
 * @package app\common\command
 */
class OrderClose extends Command
{

    protected function configure()
    {
        $this->setName('order_close')
            ->setDescription('系统关闭超时未付款订单');
    }

    protected function execute(Input $input, Output $output)
    {
        $now = time();
        $ableClose = ConfigService::get('transaction', 'cancel_unpaid_orders');
        $cancelTime = ConfigService::get('transaction', 'cancel_unpaid_orders_times') * 60;

        if ($ableClose == YesNoEnum::NO) {
            return true;
        }

        $orders = Order::with('order_goods')
            ->whereRaw("create_time+$cancelTime < $now")
            ->where([
                'order_status' => OrderEnum::STATUS_WAIT_PAY,
                'pay_status' => PayEnum::UNPAID,
            ])->select();

        if (empty($orders)) {
            return true;
        }

        try{

            foreach ($orders as $order) {
                //回退订单商品库存
                $this->rollbackGoods($order);

                //更新订单状态
                $this->updateOrderSattus($order);

                //如有使用优惠券, 返回优惠券
                AfterSaleService::returnCoupon($order);
            }

        } catch(\Exception $e) {
            Log::write('订单自动关闭失败,失败原因:' . $e->getMessage());
        }
    }



    /**
     * @notes 回退库存
     * @param $order
     * @author 段誉
     * @date 2021/9/15 14:32
     */
    protected function rollbackGoods($order)
    {
        foreach ($order['order_goods'] as $good) {
            Goods::update([
                'total_stock' => Db::raw('total_stock+' . $good['goods_num'])
            ], ['id' => $good['goods_id']]);

            //补充规格表库存
            GoodsItem::update([
                'stock' => Db::raw('stock+' . $good['goods_num'])
            ], ['id' => $good['item_id']]);
        }
    }



    /**
     * @notes 更新订单状态
     * @param $order
     * @author 段誉
     * @date 2021/9/15 14:32
     */
    protected function updateOrderSattus($order)
    {
        //更新订单状态
        Order::update(['order_status' => OrderEnum::STATUS_CLOSE], ['id' => $order['id']]);

        // 订单日志
        (new OrderLog())->record([
            'type' => OrderLogEnum::TYPE_SYSTEM,
            'channel' => OrderLogEnum::SYSTEM_CANCEL_ORDER,
            'order_id' => $order['id'],
            'operator_id' => $order['user_id'],
        ]);
    }


}