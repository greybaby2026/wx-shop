<?php
namespace app\shopapi\logic\activity;

use app\common\service\ConfigService;

class DeductService
{
    /**
     * @notes 活动余额抵扣是否生效
     *
     * ⚠️ 2026-09-24 起本功能**整体关闭**（业务决策）：
     *   关闭活动金额抵扣，改用「充值会员折扣」；充值一律进入可用余额 user_money。
     *   历史 activity_money 余额保留不动，由后台人工处理。
     *
     * 此处**硬关闭**（不再读取后台开关），避免后台误开导致
     * 「活动抵扣」与「充值会员折扣」重复优惠。
     * 如需恢复：删除本方法开头的 return，恢复下方原配置判断逻辑。
     */
    public static function check(): array
    {
        return ['active' => false, 'ratio' => 0.0, 'reason' => '活动余额抵扣已关闭'];

        /* ---------- 原实现（暂留备查，勿删） ----------
        $switch = ConfigService::get('activity_deduct', 'switch', 0);
        $start  = ConfigService::get('activity_deduct', 'start_time', '');
        $end    = ConfigService::get('activity_deduct', 'end_time', '');
        $ratio  = (float) ConfigService::get('activity_deduct', 'ratio', 40);
        $now    = time();

        if (!$switch) {
            return ['active' => false, 'ratio' => $ratio, 'reason' => '活动未开启'];
        }
        if ($start && $now < strtotime($start)) {
            return ['active' => false, 'ratio' => $ratio, 'reason' => '活动未开始'];
        }
        if ($end && $now > strtotime($end)) {
            return ['active' => false, 'ratio' => $ratio, 'reason' => '活动已结束'];
        }
        return ['active' => true, 'ratio' => $ratio, 'reason' => ''];
        ---------- 原实现结束 ---------- */
    }

    public static function calculate(float $orderAmount, float $activityMoney, float $ratio): array
    {
        if ($orderAmount <= 0 || $activityMoney <= 0 || $ratio <= 0) {
            return ['deduct_amount' => 0.00, 'pay_amount' => $orderAmount];
        }
        $maxDeduct = $orderAmount * $ratio / 100;
        $actualDeduct = min($maxDeduct, $activityMoney);
        $payAmount = $orderAmount - $actualDeduct;
        return [
            'deduct_amount' => round($actualDeduct, 2),
            'pay_amount'    => max(round($payAmount, 2), 0.00),
        ];
    }

    public static function getDisplayInfo(): array
    {
        $active = self::check();
        $themeConfig = ConfigService::get('activity_deduct', 'theme_config', []);
        return [
            'active'        => $active['active'],
            'ratio'         => $active['ratio'],
            'activity_name' => ConfigService::get('activity_deduct', 'activity_name', ''),
            'current_theme' => ConfigService::get('activity_deduct', 'current_theme', ''),
            'theme_config'  => is_array($themeConfig) ? $themeConfig : [],
        ];
    }
    /**
     * @notes 充值是否进入「活动余额」
     *
     * ⚠️ 2026-09-24 起**硬关闭**：充值一律进入可用余额 user_money。
     * 此处不再读取后台配置，避免误开导致充值又流入 activity_money。
     * 如需恢复：删除本方法开头的 return，恢复下方原实现。
     */
    public static function rechargeToActivity(): bool
    {
        return false;

        /* ---------- 原实现（暂留备查，勿删） ----------
        $switch = ConfigService::get('activity_deduct', 'switch', 0);
        $rechargeTo = ConfigService::get('activity_deduct', 'recharge_to_activity', 0);
        return $switch && $rechargeTo;
        ---------- 原实现结束 ---------- */
    }
}