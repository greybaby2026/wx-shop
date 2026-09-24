<?php
namespace app\common\logic;

use app\common\model\RechargeTemplate;
use app\common\model\User;
use app\common\service\ConfigService;
use think\facade\Log;

/**
 * 充值会员折扣（充值金额 → 永久购物折扣）
 *
 * 业务口径（已与业务方确认）：
 *   1. 总开关关闭 → 整条链路失效（不授予、不计算、前台不展示）
 *   2. 达标口径：**单笔充值金额** ≥ 档位阈值即达标，取满足条件中折扣最优（数值最小）的档位
 *   3. 永久有效且「只升不降」：已享折扣不会因档位调整而变差（数值变大）
 *   4. 档位配置只参与「单次充值是否达标」的判定，对新老用户一视同仁；不追溯补差
 *   5. 折扣生效还需：① 已绑定门店（bind_store_id > 0）② 使用余额支付
 *
 * 档位表复用 ls_recharge_template（与「充值满X送Y」同一张表、同一个后台配置页）：
 *   money    = 达标充值金额
 *   award    = 赠送余额（JSON，与本折扣无关）
 *   discount = 该档对应的永久折扣率（10=不打折，9.5=95折，0=该档不享折扣）
 *
 * ⚠️ 折扣金额的最终落库与扣减在 OrderLogic 中完成（那里能拿到商品原价、优惠券等全部上下文）；
 *    本类只负责「档位匹配 / 授予 / 生效条件判定 / 折扣额计算 / 保底截断」。
 */
class RechargeMemberDiscountLogic
{
    /** 配置组名（ls_config.type） */
    const CFG_TYPE = 'recharge_member';
    /** 总开关键名 */
    const CFG_OPEN = 'open';

    /** 折扣率哨兵：10 折 = 原价（不优惠） */
    const FULL_PRICE = 10.00;

    /** 单笔订单「总优惠」占商品原价的上限比例 —— 防运营配错档位导致穿价 */
    const MAX_DISCOUNT_RATE = 0.70;

    /**
     * @notes 总开关是否开启
     */
    public static function checkOpen(): bool
    {
        return intval(ConfigService::get(self::CFG_TYPE, self::CFG_OPEN, 0)) === 1;
    }

    /**
     * @notes 是否强制「绑定门店」后才能下单/充值
     *
     * ⚠️ 默认关闭：本拦截需在「选择门店绑定」页随客户端发版上线之后才能开启，
     *    否则老版本客户端没有绑定页，会导致所有未绑定门店的用户无法下单。
     *    与总开关一同配置（ls_config type=recharge_member）。
     */
    public static function isForceBindStore(): bool
    {
        return intval(ConfigService::get(self::CFG_TYPE, 'force_bind_store', 0)) === 1;
    }

    /**
     * @notes 档位列表（按充值金额升序）
     */
    public static function getLevels(): array
    {
        $levels = RechargeTemplate::field('id,money,award,discount')
            ->order('money asc')
            ->select()
            ->toArray();
        foreach ($levels as &$level) {
            $level['money']    = floatval($level['money']);
            $level['discount'] = floatval($level['discount'] ?? 0);
        }
        unset($level);
        return $levels;
    }

    /**
     * @notes 按「单笔实充金额」匹配最优档位
     *        取金额达标且折扣最优（discount 数值最小）的档位；无则返回 []
     * @param float $amount 单笔实充金额（注意：赠送金额不计入）
     */
    public static function matchLevelByAmount(float $amount): array
    {
        if ($amount <= 0) {
            return [];
        }
        $best = [];
        foreach (self::getLevels() as $level) {
            // 档位金额未达标 → 跳过
            if ($level['money'] <= 0 || $amount < $level['money']) {
                continue;
            }
            // 折扣无效（0 = 该档不享折扣；>=10 = 不打折）→ 跳过
            if ($level['discount'] <= 0 || $level['discount'] >= self::FULL_PRICE) {
                continue;
            }
            if (empty($best) || $level['discount'] < floatval($best['discount'])) {
                $best = $level;
            }
        }
        return $best;
    }

    /**
     * @notes 充值成功后授予折扣（幂等 + 只升不降）
     *        在 PayNotifyLogic::recharge() 内、累加 total_recharge_amount 之后调用
     * @param int   $userId
     * @param mixed $amount 本次实充金额（order_amount，不含赠送）
     */
    public static function grantOnRecharge(int $userId, $amount): bool
    {
        try {
            if (!self::checkOpen()) {
                return true;
            }
            $amount = floatval($amount);
            if ($amount <= 0) {
                return true;
            }
            $level = self::matchLevelByAmount($amount);
            if (empty($level)) {
                return true;
            }
            $newDiscount = floatval($level['discount']);
            $user = User::findOrEmpty($userId);
            if ($user->isEmpty()) {
                return true;
            }
            // 条件更新（防并发 + 天然幂等）：仅当「当前无折扣(0)」或「当前折扣差于新折扣(数值更大)」时才升级
            $affected = User::where('id', $userId)
                ->where(function ($query) use ($newDiscount) {
                    $query->where('recharge_discount', 0)
                        ->whereOr('recharge_discount', '>', $newDiscount);
                })
                ->update([
                    'recharge_discount'          => $newDiscount,
                    'recharge_discount_level_id' => intval($level['id']),
                    'recharge_discount_time'     => time(),
                ]);
            if ($affected > 0) {
                Log::write(
                    'recharge_member_discount_grant | user_id=' . $userId
                    . ' | amount=' . $amount
                    . ' | level_id=' . intval($level['id'])
                    . ' | discount=' . $newDiscount
                    . ' | old=' . floatval($user['recharge_discount'] ?? 0),
                    'info'
                );
            }
            return true;
        } catch (\Throwable $e) {
            // 授予失败不阻断充值主流程（钱已到账），但必须留可定位日志
            Log::write(
                'recharge_member_discount_error | user_id=' . $userId
                . ' | amount=' . $amount
                . ' | ' . $e->getMessage()
                . ' | ' . $e->getFile() . ':' . $e->getLine(),
                'error'
            );
            return true;
        }
    }

    /**
     * @notes 折扣生效条件（不含「余额是否充足」，余额判定由下单链路按实时余额完成）
     * @param array $user 用户数组（需含 recharge_discount / bind_store_id）
     * @return array ['usable' => bool, 'reason' => string, 'discount' => float]
     */
    public static function checkUsable(array $user): array
    {
        if (!self::checkOpen()) {
            return ['usable' => false, 'reason' => '折扣未开启', 'discount' => 0.0];
        }
        $discount = floatval($user['recharge_discount'] ?? 0);
        if ($discount <= 0 || $discount >= self::FULL_PRICE) {
            return ['usable' => false, 'reason' => '未达充值折扣标准', 'discount' => 0.0];
        }
        // 说明：只判「是否已绑定门店」，不判门店是否停用 —— 绑定为「首绑终身」，
        //      若因门店停用而剥夺折扣，用户将永久无法恢复（无法改绑），故不纳入判定
        if (intval($user['bind_store_id'] ?? 0) <= 0) {
            return ['usable' => false, 'reason' => '未绑定门店', 'discount' => $discount];
        }
        return ['usable' => true, 'reason' => '', 'discount' => $discount];
    }

    /**
     * @notes 计算折扣优惠额（折扣率 9.5 → 优惠 5%）
     * @param float $baseAmount 折扣基数（商品实付金额，不含运费）
     * @param float $discount   折扣率（10 = 不打折）
     */
    public static function calcDiscountAmount(float $baseAmount, float $discount): float
    {
        if ($baseAmount <= 0 || $discount <= 0 || $discount >= self::FULL_PRICE) {
            return 0.0;
        }
        return round($baseAmount * (1 - $discount / self::FULL_PRICE), 2);
    }

    /**
     * @notes 保底截断：单笔订单总优惠（等级折扣 + 充值折扣 + 优惠券）不超过商品原价的 70%
     *        仅对「充值折扣额」做截断（不动优惠券），超出部分记告警日志
     * @param float $originalPrice  参与折扣的商品原价合计
     * @param float $otherDiscount  其它优惠合计（优惠券等，不含充值折扣）
     * @param float $discountAmount 本次充值折扣额
     * @return float 截断后的充值折扣额
     */
    public static function capDiscount(float $originalPrice, float $otherDiscount, float $discountAmount): float
    {
        if ($discountAmount <= 0 || $originalPrice <= 0) {
            return max($discountAmount, 0);
        }
        // 其它优惠已超额 → 本次折扣不给
        $maxTotal = round($originalPrice * self::MAX_DISCOUNT_RATE, 2);
        $remain   = round($maxTotal - $otherDiscount, 2);
        if ($remain <= 0) {
            Log::write(
                'recharge_member_discount_capped | original=' . $originalPrice
                . ' | other=' . $otherDiscount . ' | want=' . $discountAmount . ' | allow=0',
                'error'
            );
            return 0.0;
        }
        if ($discountAmount > $remain) {
            Log::write(
                'recharge_member_discount_capped | original=' . $originalPrice
                . ' | other=' . $otherDiscount . ' | want=' . $discountAmount . ' | allow=' . $remain,
                'error'
            );
            return $remain;
        }
        return $discountAmount;
    }
}
