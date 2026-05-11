<?php

namespace app\common\logic;

use app\common\enum\AccountLogEnum;
use app\common\model\RechargeCommission;
use app\common\model\RechargeOrder;
use app\common\model\User;
use app\common\service\ConfigService;
use think\facade\Db;
use think\facade\Log;

class RechargeCommissionLogic extends BaseLogic
{
    public static function settle(RechargeOrder $rechargeOrder)
    {
        $open = ConfigService::get('recharge_distribution', 'open');
        if (!$open) {
            return true;
        }

        $fromUser = User::findOrEmpty($rechargeOrder->user_id);
        if ($fromUser->isEmpty()) {
            return true;
        }

        $firstRatio = ConfigService::get('recharge_distribution', 'first_ratio', 0);
        $secondRatio = ConfigService::get('recharge_distribution', 'second_ratio', 0);
        $maxCommission = ConfigService::get('recharge_distribution', 'max_commission', 0);

        $rechargeAmount = $rechargeOrder->order_amount;
        $commissionList = [];

        if ($fromUser->first_leader > 0 && $firstRatio > 0) {
            $earnings = bcmul($rechargeAmount, bcdiv($firstRatio, 100, 4), 2);
            if ($maxCommission > 0 && bccomp($earnings, $maxCommission, 2) > 0) {
                $earnings = $maxCommission;
            }
            if ($earnings > 0) {
                $commissionList[] = [
                    'user_id' => $fromUser->first_leader,
                    'level' => 1,
                    'ratio' => $firstRatio,
                    'earnings' => $earnings,
                ];
            }
        }

        if ($fromUser->second_leader > 0 && $secondRatio > 0) {
            $earnings = bcmul($rechargeAmount, bcdiv($secondRatio, 100, 4), 2);
            if ($maxCommission > 0 && bccomp($earnings, $maxCommission, 2) > 0) {
                $earnings = $maxCommission;
            }
            if ($earnings > 0) {
                $commissionList[] = [
                    'user_id' => $fromUser->second_leader,
                    'level' => 2,
                    'ratio' => $secondRatio,
                    'earnings' => $earnings,
                ];
            }
        }

        if (empty($commissionList)) {
            return true;
        }

        Db::startTrans();
        try {
            foreach ($commissionList as $item) {
                $user = User::findOrEmpty($item['user_id']);
                if ($user->isEmpty()) {
                    continue;
                }

                $user->user_earnings = bcadd($user->user_earnings, $item['earnings'], 2);
                $user->save();

                AccountLogLogic::add(
                    $item['user_id'],
                    AccountLogEnum::BW_INC_RECHARGE_COMMISSION,
                    AccountLogEnum::INC,
                    $item['earnings'],
                    $rechargeOrder->sn,
                    '充值佣金'
                );

                RechargeCommission::create([
                    'sn' => generate_sn(new RechargeCommission(), 'sn'),
                    'recharge_order_id' => $rechargeOrder->id,
                    'recharge_order_sn' => $rechargeOrder->sn,
                    'user_id' => $item['user_id'],
                    'from_user_id' => $rechargeOrder->user_id,
                    'level' => $item['level'],
                    'ratio' => $item['ratio'],
                    'recharge_amount' => $rechargeAmount,
                    'earnings' => $item['earnings'],
                    'status' => 1,
                    'settle_time' => time(),
                    'create_time' => time(),
                ]);
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            Log::write('充值佣金结算失败: ' . $e->getMessage(), 'RechargeCommission');
            self::setError($e->getMessage());
            return false;
        }
    }
}
