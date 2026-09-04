<?php
namespace app\businessapi\logic;

use app\common\enum\AccountLogEnum;
use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\enum\UserTerminalEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\model\Order;
use app\common\model\OrderGoods;
use app\common\model\User;
use app\common\service\ConfigService;
use app\shopapi\logic\activity\DeductService;
use think\facade\Db;

class ErpLogic extends BaseLogic
{
    public function sendCode($params): array|false
    {
        $user = User::where('mobile', $params['mobile'])->field('id,sn,nickname,mobile')->findOrEmpty();
        if ($user->isEmpty()) { self::$error = '该手机号未注册'; return false; }

        $code = mt_rand(1000, 9999);
        $result = event('Notice', [
            'scene_id' => \app\common\enum\NoticeEnum::LOGIN_CAPTCHA,
            'params' => ['mobile' => $user->mobile, 'code' => $code]
        ]);
        if (empty($result) || $result[0] !== true) {
            self::$error = '短信发送失败';
            return false;
        }
        cache('erp_sms_' . $user->mobile, $code, 300);
        return ['mobile' => $user->mobile, 'nickname' => $user->nickname, 'expire_minutes' => 5];
    }

    public function userInfo($params): array
    {
        $query = User::field('id,sn,nickname,avatar,user_money,activity_money,six_discount,mobile');
        if (!empty($params['user_sn'])) { $query->where('sn', $params['user_sn']); }
        elseif (!empty($params['mobile'])) { $query->where('mobile', $params['mobile']); }
        else { self::$error = '请输入手机号或用户编号'; return []; }
        $user = $query->findOrEmpty();
        if ($user->isEmpty()) { self::$error = '用户不存在'; return []; }
        $deductInfo = DeductService::check();
        return [
            'user_id' => $user->id, 'sn' => $user->sn, 'nickname' => $user->nickname,
            'mobile' => $user->mobile, 'avatar' => $user->avatar,
            'activity_money' => round((float)($user->activity_money ?? 0), 2),
            'user_money' => round((float)($user->user_money ?? 0), 2),
            'deduct_ratio' => $deductInfo['active'] ? $deductInfo['ratio'] : 0,
            'deduct_active' => $deductInfo['active'],
            'six_discount' => (int)($user->six_discount ?? 0),
        ];
    }

    public function createOrder($params)
    {
        $cachedCode = cache('erp_sms_' . $params['mobile']);
        if (empty($cachedCode) || $cachedCode != $params['sms_code']) {
            self::$error = '验证码错误或已过期'; return false;
        }
        cache('erp_sms_' . $params['mobile'], null);

        Db::startTrans();
        try {
            $user = User::where('mobile', $params['mobile'])->findOrEmpty();
            if ($user->isEmpty()) throw new \Exception('用户不存在');

            $deductInfo = DeductService::check();
            $deductRatio = isset($params['deduct_ratio']) ? (float)$params['deduct_ratio'] : (float)ConfigService::get('activity_deduct', 'ratio', 40);
            $deductRatio = max(0, min($deductRatio, 100));
            $totalAmount = round((float)$params['total_amount'], 2);
            $deductAmount = 0.00;

            if ($deductInfo['active'] && $user->activity_money > 0 && $deductRatio > 0) {
                $calc = DeductService::calculate($totalAmount, (float)$user->activity_money, $deductRatio);
                $deductAmount = $calc['deduct_amount'];
            }

            $payAmount = max(round($totalAmount - $deductAmount, 2), 0.00);

            // 支付方式映射: 字符串 -> 整数
            $payWayMap = ['offline_cash'=>PayEnum::OFFLINE_PAY,'offline_card'=>PayEnum::OFFLINE_PAY,'offline_pos'=>PayEnum::OFFLINE_PAY,'user_money'=>PayEnum::BALANCE_PAY];
            $payWay = $payWayMap[$params['pay_way'] ?? ''] ?? PayEnum::OFFLINE_PAY;

            if ($deductAmount > 0) {
                $user->activity_money = bcsub((string)$user->activity_money, (string)$deductAmount, 2);
                $user->save();
            }

            $orderSn = !empty($params['order_sn']) ? $params['order_sn'] : generate_sn((new Order()), 'sn');
            // 简约ERP订单：只写必要字段，其余给默认值避免后台报错
            $order = Order::create([
                'sn' => $orderSn,
                'order_type' => OrderEnum::ERP_ORDER,
                'user_id' => $user->id,
                'order_terminal' => UserTerminalEnum::ERP,
                'total_num' => array_sum(array_column($params['goods_list'], 'goods_num')),
                'total_amount' => $totalAmount,
                'goods_price' => $totalAmount,
                'order_amount' => $totalAmount,
                'express_price' => 0.00,
                'discount_amount' => 0.00,
                'member_amount' => 0.00,
                'deduct_amount' => $deductAmount,
                'pay_amount' => $payAmount,
                'user_remark' => ($params['remark'] ?? '') . ' | 活动抵扣: ' . $deductAmount . ' 元 (比例' . $deductRatio . '%)',
                'address' => ['contact' => $user->nickname ?? '', 'mobile' => $params['mobile'] ?? '', 'province' => '', 'city' => '', 'district' => '', 'address' => ''],
                'pay_status' => PayEnum::ISPAID,
                'pay_way' => $payWay,
                'order_status' => OrderEnum::STATUS_FINISH,
                'pay_time' => time(),
                'delivery_type' => 2,
                'express_status' => 0,
                'verification_status' => 0,
            ]);

            foreach ($params['goods_list'] as $goods) {
                $price = round((float)$goods['goods_price'], 2);
                $num = (int)$goods['goods_num'];
                OrderGoods::create([
                    'order_id' => $order->id, 'goods_id' => $goods['goods_id'] ?? 0,
                    'item_id' => $goods['item_id'] ?? 0, 'goods_name' => $goods['goods_name'],
                    'goods_price' => $price, 'goods_num' => $num,
                    'total_price' => round($price * $num, 2),
                    'total_pay_price' => round($price * $num, 2),
                    'goods_snap' => json_encode(['goods_name' => $goods['goods_name'], 'image' => '', 'item_image' => '', 'spec_value_str' => $goods['spec_str'] ?? '', 'sell_price' => (string)$price, 'original_price' => (string)$price, 'bar_code' => '']), 'spec_value_str' => $goods['spec_str'] ?? '',
                ]);
            }

            if ($deductAmount > 0) {
                AccountLogLogic::add($user->id, AccountLogEnum::BNW_DEC_ORDER, AccountLogEnum::DEC, $deductAmount, $orderSn, '门店ERP订单活动抵扣', [], 'activity_money');
            }

            if ($payWay === PayEnum::BALANCE_PAY && $payAmount > 0) {
                if ($user->user_money < $payAmount) throw new \Exception('用户余额不足');
                $user->user_money = bcsub((string)$user->user_money, (string)$payAmount, 2);
                $user->save();
                AccountLogLogic::add($user->id, AccountLogEnum::BNW_DEC_ORDER, AccountLogEnum::DEC, $payAmount, $orderSn, '门店ERP余额支付');
            }

            Db::commit();
            return ['order_id'=>$order->id,'order_sn'=>$orderSn,'deduct_amount'=>$deductAmount,'pay_amount'=>$payAmount,'total_amount'=>$totalAmount];
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    public function confirmPay($params)
    {
        $order = Order::findOrEmpty($params['order_id']);
        if ($order->isEmpty()) { self::$error = '订单不存在'; return false; }
        if ($order->pay_status == PayEnum::ISPAID) { self::$error = '订单已支付'; return false; }
        $payWayMap = ['offline_cash'=>PayEnum::OFFLINE_PAY,'offline_card'=>PayEnum::OFFLINE_PAY,'offline_pos'=>PayEnum::OFFLINE_PAY,'user_money'=>PayEnum::BALANCE_PAY];
        $order->pay_status = PayEnum::ISPAID;
        $order->pay_time = time();
        $order->pay_way = $payWayMap[$params['pay_way'] ?? ''] ?? PayEnum::OFFLINE_PAY;
        $order->save();
        return true;
    }
}
