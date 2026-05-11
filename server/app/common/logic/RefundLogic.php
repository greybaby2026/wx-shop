<?php
// +----------------------------------------------------------------------
// | LikeShop有特色的全开源社交分销电商系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 商业用途务必购买系统授权，以免引起不必要的法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | 微信公众号：好象科技
// | 访问官网：http://www.likemarket.net
// | 访问社区：http://bbs.likemarket.net
// | 访问手册：http://doc.likemarket.net
// | 好象科技开发团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | Author: LikeShopTeam-段誉
// +----------------------------------------------------------------------


namespace app\common\logic;



use app\common\enum\AfterSaleEnum;
use app\common\enum\AfterSaleLogEnum;
use app\common\enum\DeliveryEnum;
use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\model\AfterSale;
use app\common\model\Order;
use app\common\model\OrderGoods;
use app\common\model\Refund;
use app\common\service\after_sale\AfterSaleService;
use app\common\service\pay\AliPayService;
use app\common\service\pay\BalancePayService;
use app\common\service\pay\ToutiaoPayService;
use app\common\service\pay\WeChatPayService;
use app\common\service\WeChatConfigService;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 订单退款逻辑
 * Class OrderRefundLogic
 * @package app\common\logic
 */
class RefundLogic extends BaseLogic
{

    protected static $refund;

    /**
     * @notes 发起退款
     * @param $refundWay //退款类型;(原路退,退回到余额)
     * @param $order //订单信息
     * @param $afterSaleId //售后退款id
     * @param $refundAmount //退款金额
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @author 段誉
     * @date 2021/8/9 17:31
     */
    public static function refund($refundWay, $order, $afterSaleId, $refundAmount)
    {
        if ($refundAmount < 0) {
            return false;
        }

        self::log($order, $afterSaleId, $refundAmount);

        if ($refundAmount == 0) {
            //更新退款日志记录
            Refund::update([
                'refund_status' => 1
            ], ['id' => self::$refund['id']]);

            //更新售后状态
            $afterSale = AfterSale::findOrEmpty($afterSaleId);
            // 整单退款
            if($afterSale['refund_type'] == AfterSaleEnum::REFUND_TYPE_ORDER) {
                $order = Order::findOrEmpty($afterSale['order_id'])->toArray();
                $refundStauts = ($afterSale['refund_total_amount'] == $order['order_amount']) ? AfterSaleEnum::FULL_REFUND : AfterSaleEnum::PARTIAL_REFUND;
            }
            // 商品售后
            if($afterSale['refund_type'] == AfterSaleEnum::REFUND_TYPE_GOODS) {
                $orderGoods = OrderGoods::findOrEmpty($afterSale['order_goods_id'])->toArray();
                $refundStauts = ($afterSale['refund_total_amount'] == $orderGoods['total_pay_price']) ? AfterSaleEnum::FULL_REFUND : AfterSaleEnum::PARTIAL_REFUND;
            }
            $afterSale->status = AfterSaleEnum::STATUS_SUCCESS;
            $afterSale->sub_status = AfterSaleEnum::SUB_STATUS_SELLER_REFUND_SUCCESS;
            $afterSale->refund_status = $refundStauts ?? AfterSaleEnum::FULL_REFUND;
            $afterSale->save();
            AfterSaleService::createAfterLog($afterSale['id'], '系统已完成退款', 0, AfterSaleLogEnum::ROLE_SYS);

            return true;
        }

        //区分原路退 还是退回到余额
        if ($refundWay == AfterSaleEnum::REFUND_WAYS_BALANCE) {
            self::balancePayRefund($order, $refundAmount, $afterSaleId);
            return true;
        }

        switch ($order['pay_way']) {
            //余额退款
            case PayEnum::BALANCE_PAY:
                self::balancePayRefund($order, $refundAmount,$afterSaleId);
                break;
            //微信退款
            case PayEnum::WECHAT_PAY:
                self::wechatPayRefund($order, $refundAmount);
                break;
            //支付宝退款
            case PayEnum::ALI_PAY:
                self::aliPayRefund($order, $refundAmount);
                break;
            //字节退款
            case PayEnum::BYTE_PAY:
                self::bytePayRefund($order, $refundAmount);
                break;
            // 线下支付 直接完成
            case PayEnum::OFFLINE_PAY:
                return true;
                break;
        }

        return true;
    }



    /**
     * @notes 余额退款
     * @param $order
     * @param $refundAmount
     * @author 段誉
     * @date 2021/8/5 10:25
     */
    public static function balancePayRefund($order, $refundAmount,$afterSaleId)
    {
        (new BalancePayService())->refund($order, $refundAmount,$afterSaleId);
    }



    /**
     * @notes 微信退款
     * @param $refundWay
     * @param $order
     * @param $refundAmount
     * @return bool|void
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @author 段誉
     * @date 2021/8/5 10:25
     */
    public static function wechatPayRefund($order, $refundAmount)
    {
        $pay = new WeChatPayService($order['order_terminal']);
        $result = $pay->refund([
            'transaction_id'    => $order['transaction_id'],
            'refund_sn'         => self::$refund['sn'],
            'total_fee'         => $order['order_amount'],
            'refund_fee'        => $refundAmount,
        ]);
        
        if ($result !== true) {
            throw new \Exception($pay->realPay()->getMessage());
        }
        
        //更新退款日志记录
        Refund::update([
            'wechat_refund_id'  => 0,
            'refund_status'     => 1,
            'refund_msg'        => json_encode([], JSON_UNESCAPED_UNICODE),
        ], ['id' => self::$refund['id']]);
    }


    /**
     * @notes 支付宝退款
     * @param $order
     * @param $refundAmount
     * @return bool
     * @throws \Exception
     * @author 段誉
     * @date 2021/8/5 10:25
     */
    public static function aliPayRefund($order, $refundAmount)
    {
        //原路退回到支付宝的情况
        $result = (new AliPayService())->refund($order['sn'], $refundAmount, self::$refund['sn']);
        $result = (array)$result;

        //更新退款日志记录
        Refund::update([
            'refund_status' => (isset($result['result_code']) && $result['result_code'] == 'SUCCESS') ? 1 : 2,
            'refund_msg' => json_encode($result['httpBody'], JSON_UNESCAPED_UNICODE),
        ], ['id' => self::$refund['id']]);

        if ($result['code'] != '10000' || $result['msg'] != 'Success' || $result['fundChange'] != 'Y') {
            throw new \Exception('支付宝退款失败');
        }

        return true;
    }

    /**
     * @notes 字节退款
     * @param $order
     * @param $refundAmount
     * @author Tab
     * @date 2021/11/18 14:09
     */
    public static function bytePayRefund($order, $refundAmount)
    {
        (new ToutiaoPayService())->refund($order, $refundAmount, self::$refund);
    }



    /**
     * @notes 退款日志
     * @param $order
     * @param $afterSaleId
     * @param $refundAmount
     * @author 段誉
     * @date 2021/8/9 17:32
     */
    public static function log($order, $afterSaleId, $refundAmount)
    {
        $result = Refund::create([
            'order_id' => $order['id'],
            'after_sale_id' => $afterSaleId,
            'user_id' => $order['user_id'],
            'sn' => generate_sn(new Refund(), 'sn'),
            'order_amount' => $order['order_amount'],
            'refund_amount' => $refundAmount,
        ]);

        self::$refund = $result;
    }
    
    /**
     * @notes 售后退款更新订单或订单商品状态
     * @param $order_id
     * @return void
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     * @author lbzy
     * @datetime 2023-07-07 13:53:19
     */
    static function afterSaleRefundUpdate($order_id): void
    {
        $order_goods_count  = OrderGoods::where('order_id', $order_id)->count();
        
        $after_sale_count   = AfterSale::where('order_id', $order_id)
            ->group('order_goods_id')
            ->where('status', AfterSaleEnum::STATUS_SUCCESS)
            ->count();

        //如果订单商品已全部退款
        if ($order_goods_count == $after_sale_count) {
            Order::update([ 'order_status' => OrderEnum::STATUS_CLOSE ], [
                [ 'id', '=', $order_id ],
            ]);
        } else {
            // 更新订单发货状态
            $orderGoodsIds = OrderGoods::where(['order_id'=>$order_id,'express_status'=>[DeliveryEnum::NOT_SHIPPED,DeliveryEnum::PART_SHIPPED]])->column('id');
            if (!empty($orderGoodsIds)) {
                foreach ($orderGoodsIds as $key=>$orderGoodsId) {
                    $afterSale = AfterSale::where(['order_goods_id'=>$orderGoodsId,'status'=>AfterSaleEnum::STATUS_SUCCESS])->findOrEmpty();
                    if (!$afterSale->isEmpty()) {
                        unset($orderGoodsIds[$key]);
                    }
                }
            }
            if (empty($orderGoodsIds)) {
                $order = Order::find($order_id);
                $order->order_status = max($order->order_status, OrderEnum::STATUS_WAIT_RECEIVE);
                $order->express_status = DeliveryEnum::SHIPPED;
                $order->save();
            }
        }
    }
}