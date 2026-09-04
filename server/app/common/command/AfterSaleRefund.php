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

use app\common\enum\AccountLogEnum;
use app\common\enum\AfterSaleEnum;
use app\common\enum\AfterSaleLogEnum;
use app\common\enum\BargainEnum;
use app\common\enum\NoticeEnum;
use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\logic\RefundLogic;
use app\common\model\AccountLog;
use app\common\model\AfterSale;
use app\common\model\BargainInitiate;
use app\common\model\Order;
use app\common\model\OrderGoods;
use app\common\model\Refund;
use app\common\service\after_sale\AfterSaleService;
use app\common\service\pay\AliPayService;
use app\common\service\pay\ToutiaoPayService;
use app\common\service\pay\WeChatPayService;
use app\common\service\WeChatConfigService;
use EasyWeChat\Factory;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Log;

/**
 * 售后退款查询
 * 只查询微信退款、支付宝退款两种退款方式，退回余额方式是马上到账的得到售后结果的，无需查询
 * Class DistributionSettlement
 * @package app\common\command
 */
class AfterSaleRefund extends Command
{
    protected function configure()
    {
        $this->setName('after_sale_refund')
            ->setDescription('售后退款查询');
    }

    protected function execute(Input $input, Output $output)
    {
        // 查找售后中：售后退款中记录
        $afterSaleList = AfterSale::where('sub_status', AfterSaleEnum::SUB_STATUS_SELLER_REFUND_ING)->select()->toArray();
        if(empty($afterSaleList)) {
            return true;
        }
        foreach($afterSaleList as $item) {
            switch ($item['refund_way']) {
                // 原路退回
                case AfterSaleEnum::REFUND_WAYS_ORIGINAL:
                    $result = self::originalRefund($item);
                    break;
                default:
                    $result = null;
            }

            // 退款成功
            if($result === true) {
                self::afterSuccess($item);
                continue;
            }
            // 退款失败
            if($result === false) {
                self::afterFail($item);
                continue;
            }
        }

    }


    /**
     * @notes 查询微信退款是否成功
     * @param $item
     * @author Tab
     * @date 2021/9/11 11:33
     */
    public static function checkWechatRefund($item)
    {
        $order = Order::findOrEmpty($item['order_id'])->toArray();
        
        $pay = new WeChatPayService($order['order_terminal']);
        
        // 获取售后单对应的退款记录
        $refund = Refund::where('after_sale_id', $item['id'])->findOrEmpty();
        // 根据商户退款单号查询退款
        $result = $pay->queryRefund($refund);
        
        if (is_bool($result)) {
            return $result;
        }

        // 其他情况,将查询结果写入到退款记录中
        $refund->refund_msg = json_encode($result, JSON_UNESCAPED_UNICODE);
        $refund->save();

        return null;
    }

    /**
     * @notes 查询支付宝退款是否成功
     * @param $item
     * @author Tab
     * @date 2021/9/13 10:34
     */
    public static function checkAliRefund($item)
    {
        $order = Order::findOrEmpty($item['order_id'])->toArray();
        // 获取售后单对应的退款记录
        $refund = Refund::where('after_sale_id', $item['id'])->findOrEmpty()->toArray();
        $result = (new AliPayService())->queryRefund($order['sn'], $refund['sn']);
        $result = $result->toMap();
        if ($result['code'] == '10000' && $result['msg'] == 'Success' && $result['refund_status'] == 'REFUND_SUCCESS') {
            // 退款成功
            return true;
        }

        // 退款查询请求未收到 或 退款失败
        return null;
    }

    /**
     * @notes 校验字节退款
     * @param $item
     * @throws \Exception
     * @author Tab
     * @date 2021/11/18 14:51
     */
    public static function checkByteRefund($item)
    {
        // 获取售后单对应的退款记录
        $refund = Refund::where('after_sale_id', $item['id'])->findOrEmpty()->toArray();
        return (new ToutiaoPayService())->queryRefund($refund['sn']);
    }

    /**
     * @notes 计算退款状态
     * @param $item
     * @return int
     * @author Tab
     * @date 2021/8/18 11:51
     */
    public static function calcRefundStatus($item)
    {
        // 整单退款
        if($item['refund_type'] == AfterSaleEnum::REFUND_TYPE_ORDER) {
            $order = Order::findOrEmpty($item['order_id'])->toArray();
            return $item['refund_total_amount'] == $order['order_amount'] ? AfterSaleEnum::FULL_REFUND : AfterSaleEnum::PARTIAL_REFUND;
        }
        // 商品售后
        if($item['refund_type'] == AfterSaleEnum::REFUND_TYPE_GOODS) {
            $orderGoods = OrderGoods::findOrEmpty($item['order_goods_id'])->toArray();
            return $item['refund_total_amount'] == $orderGoods['total_pay_price'] ? AfterSaleEnum::FULL_REFUND : AfterSaleEnum::PARTIAL_REFUND;
        }
    }

    /**
     * @notes 校验原路退款
     * @param $item
     * @return bool
     * @author Tab
     * @date 2021/8/18 14:31
     */
    public static function originalRefund($item)
    {
        $order = Order::findOrEmpty($item['order_id'])->toArray();
        if (empty($order)) {
            return null;
        }
        switch($order['pay_way']) {
            case PayEnum::WECHAT_PAY:
                return self::checkWechatRefund($item);
            case PayEnum::ALI_PAY:
                return self::checkAliRefund($item);
            case PayEnum::BYTE_PAY:
                return self::checkByteRefund($item);
            // 线下 直接完成
            case PayEnum::OFFLINE_PAY:
                return true;
        }
    }

    /**
     * @notes 退款成功后操作
     * @param $item
     * @author Tab
     * @date 2021/8/18 14:20
     */
    public static function afterSuccess($item)
    {
        $refundStauts = self::calcRefundStatus($item);
        AfterSale::update([
                'id' => $item['id'],
                'refund_status' => $refundStauts,
                'status' => AfterSaleEnum::STATUS_SUCCESS,
                'sub_status' => AfterSaleEnum::SUB_STATUS_SELLER_REFUND_SUCCESS
            ]);
        AfterSaleService::createAfterLog($item['id'], '系统已完成退款', 0, AfterSaleLogEnum::ROLE_SYS);
        
        //更新订单状态
        RefundLogic::afterSaleRefundUpdate($item['order_id']);

        // 扣除已赠送积分
        RefundLogic::deductAwardIntegral($item['order_id'], $item['refund_total_amount']);

        $order = Order::findOrEmpty($item['order_id'])->toArray();

        // 消息通知
        event('Notice', [
            'scene_id' => NoticeEnum::REFUND_SUCCESS_NOTICE,
            'params' => [
                'user_id' => $item['user_id'],
                'after_sale_sn' => $item['sn'],
                'order_sn' => $order['sn'],
                'refund_type' => AfterSaleEnum::getRefundTypeDesc($item['refund_way']),
                'refund_total_amount' => $item['refund_total_amount'],
                'refund_time' => date('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * @notes 退款失败后操作
     * @param $item
     * @author Tab
     * @date 2021/8/18 14:21
     */
    public static function afterFail($item)
    {
        AfterSale::update([
                'id' => $item['id'],
                'sub_status' => AfterSaleEnum::SUB_STATUS_SELLER_REFUND_FAIL
            ]);
        AfterSaleService::createAfterLog($item['id'], '系统退款失败,等待卖家处理', 0, AfterSaleLogEnum::ROLE_SYS);
    }
}