<?php
// +----------------------------------------------------------------------
// | LikeShop100%开源免费商用电商系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | Gitee下载：https://gitee.com/likeshop_gitee/likeshop
// | 访问官网：https://www.likemarket.net
// | 访问社区：https://home.likemarket.net
// | 访问手册：http://doc.likemarket.net
// | 微信公众号：好象科技
// | 好象科技开发团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------

// | Author: LikeShopTeam
// +----------------------------------------------------------------------


namespace app\common\service\pay;


use app\common\enum\AccountLogEnum;
use app\common\enum\AfterSaleEnum;
use app\common\enum\AfterSaleLogEnum;
use app\common\enum\BargainEnum;
use app\common\enum\NoticeEnum;
use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\logic\AccountLogLogic;
use app\common\model\AfterSale;
use app\common\model\BargainInitiate;
use app\common\model\User;
use app\common\service\after_sale\AfterSaleService;
use app\common\service\pay\base\StatusTrait;


/**
 * 余额支付
 * Class BalancePayService
 * @package app\common\server
 */
class BalancePayService extends BasePayService
{

    use StatusTrait;
    
    function realPay()
    {
        return $this;
    }
    
    /**
     * @notes 活动余额抵扣（混合支付时先扣活动余额部分）
     * @param $order
     * @param $deductAmount
     * @return bool
     */
    public function deduct($order, $deductAmount)
    {
        try {
            $user = User::findOrEmpty($order['user_id']);
            if ($user->isEmpty() || $user['activity_money'] < $deductAmount) {
                throw new \Exception('活动余额不足');
            }

            // 扣减活动余额
            User::update([
                'activity_money' => ['dec', $deductAmount]
            ], ['id' => $order['user_id']]);

            // 活动余额流水
            AccountLogLogic::add(
                $order['user_id'],
                AccountLogEnum::BNW_DEC_ORDER,
                AccountLogEnum::DEC,
                $deductAmount,
                $order['sn'],
                '活动余额抵扣'
            );

            return true;
        } catch (\Exception $e) {
            $this->setStatus(false, $e->getMessage());
            return false;
        }
    }

    /**
     * @notes 余额支付
     * @param $from //订单类型 (order-普通商品订单, recharge-充值订单, ....)
     * @param $order //订单信息
     * @return array|false
     * @author 段誉
     * @date 2021/8/13 16:49
     */
    public function pay($from, $order)
    {
        try {
            $deductAmount = $order['deduct_amount'] ?? 0;
            $payAmount = $order['order_amount'] - $deductAmount;
            if ($payAmount < 0) $payAmount = 0;

            $user = User::findOrEmpty($order['user_id']);
            if ($user->isEmpty() || $user['user_money'] < $payAmount) {
                throw new \Exception('余额不足');
            }

            //扣除余额
            User::update([
                'user_money' => ['dec', $payAmount]
            ], ['id' => $order['user_id']]);

            //余额流水
            AccountLogLogic::add(
                $order['user_id'],
                AccountLogEnum::BNW_DEC_ORDER,
                AccountLogEnum::DEC,
                $payAmount,
                $order['sn']
            );

            return [
                'pay_way' => PayEnum::BALANCE_PAY
            ];
        } catch (\Exception $e) {
            $this->setStatus(false, $e->getMessage());
            return false;
        }
    }


    /**
     * @notes 余额退款
     * @param $order
     * @param $refundAmount
     * @author 段誉
     * @date 2021/8/12 18:01
     */
    public function refund($order, $refundAmount,$afterSaleId)
    {
        //返回余额
        User::update([
            'user_money' => ['inc', $refundAmount]
        ], ['id' => $order['user_id']]);

        //余额流水
        $afterSale = AfterSale::findOrEmpty($afterSaleId);
        AccountLogLogic::add(
            $order['user_id'],
            AccountLogEnum::BNW_INC_AFTER_SALE,
            AccountLogEnum::INC,
            $refundAmount,
            $afterSale->sn
        );

        // 更新售后状态
        $afterSale->status = AfterSaleEnum::STATUS_SUCCESS;
        $afterSale->sub_status = AfterSaleEnum::SUB_STATUS_SELLER_REFUND_SUCCESS;
        $afterSale->refund_status = AfterSaleEnum::FULL_REFUND;
        $afterSale->save();

        AfterSaleService::createAfterLog($afterSale->id, '系统已完成退款', 0, AfterSaleLogEnum::ROLE_SYS);


        // 消息通知
        event('Notice', [
            'scene_id' => NoticeEnum::REFUND_SUCCESS_NOTICE,
            'params' => [
                'user_id' => $afterSale->user_id,
                'order_sn' => $order['sn'],
                'after_sale_sn' => $afterSale->sn,
                'refund_type' => AfterSaleEnum::getRefundTypeDesc($afterSale->refund_type),
                'refund_total_amount' => $afterSale->refund_total_amount,
                'refund_time' => date('Y-m-d H:i:s'),
            ]
        ]);
    }




    /**
     * @notes 积分订单退款
     * @param $order
     * @param $refundAmount
     * @author 段誉
     * @date 2022/4/1 10:40
     */
    public static function integralOrderRefund($order, $refundAmount)
    {
        User::where(['id' => $order['user_id']])->update([
            'user_money' => ['inc', $refundAmount]
        ]);

        AccountLogLogic::add(
            $order['user_id'],
            AccountLogEnum::BNW_INC_AFTER_SALE,
            AccountLogEnum::INC,
            $refundAmount,
            $order['user_sn']
        );
    }

}