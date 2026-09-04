<?php
namespace app\shopapi\logic\activity;

use app\common\service\ConfigService;

class DeductService
{
    public static function check(): array
    {
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
    public static function rechargeToActivity(): bool
    {
        $switch = ConfigService::get('activity_deduct', 'switch', 0);
        $rechargeTo = ConfigService::get('activity_deduct', 'recharge_to_activity', 0);
        return $switch && $rechargeTo;
    }
}