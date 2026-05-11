<?php

namespace app\openapi\logic;

use app\common\enum\AfterSaleEnum;
use app\common\enum\DistributionOrderGoodsEnum;
use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\AfterSale;
use app\common\model\AfterSaleGoods;
use app\common\model\Distribution;
use app\common\model\DistributionLevel;
use app\common\model\DistributionOrderGoods;
use app\common\model\Order;
use app\common\model\OrderGoods;
use app\common\model\RechargeCommission;
use app\common\model\User;
use think\facade\Db;

class OpenApiLogic extends BaseLogic
{
    private static function formatTime($timestamp): string
    {
        if (empty($timestamp)) {
            return '';
        }
        if (is_numeric($timestamp)) {
            if (intval($timestamp) <= 0) {
                return '';
            }
            return date('Y-m-d H:i:s', intval($timestamp));
        }
        if (is_string($timestamp) && preg_match('/^\d{4}-\d{2}-\d{2}/', $timestamp)) {
            return $timestamp;
        }
        return '';
    }

    public static function getUserOrderAmount($params)
    {
        $userId = intval($params['user_id'] ?? 0);
        $startTime = $params['start_time'] ?? '';
        $endTime = $params['end_time'] ?? '';
        $orderStatus = $params['order_status'] ?? '';

        if ($userId <= 0) {
            return self::setError('user_id参数必填');
        }

        $user = User::findOrEmpty($userId);
        if ($user->isEmpty()) {
            return self::setError('用户不存在');
        }

        $query = Order::where('user_id', $userId)
            ->where('pay_status', PayEnum::ISPAID);

        if ($orderStatus !== '') {
            $query->where('order_status', intval($orderStatus));
        }
        if (!empty($startTime)) {
            $query->where('create_time', '>=', strtotime($startTime));
        }
        if (!empty($endTime)) {
            $query->where('create_time', '<=', strtotime($endTime . ' 23:59:59'));
        }

        $totalOrderAmount = (clone $query)->sum('order_amount');
        $totalOrderNum = (clone $query)->count();
        $totalPayAmount = (clone $query)->sum('total_amount');
        $totalDiscountAmount = (clone $query)->sum('discount_amount');

        $orderList = (clone $query)
            ->field('id,sn,order_amount,total_amount,discount_amount,order_status,pay_status,pay_way,pay_time,create_time')
            ->order('id', 'desc')
            ->select()
            ->toArray();

        foreach ($orderList as &$item) {
            $item['order_status_desc'] = OrderEnum::getOrderStatusDesc($item['order_status']);
            $item['pay_time'] = self::formatTime($item['pay_time']);
            $item['create_time'] = self::formatTime($item['create_time']);
        }

        return [
            'user_info' => [
                'user_id' => $user->id,
                'nickname' => $user->nickname,
                'mobile' => $user->mobile,
                'sn' => $user->sn,
                'total_order_amount' => $user->total_order_amount,
                'total_order_num' => $user->total_order_num,
                'user_money' => $user->user_money,
                'user_earnings' => $user->user_earnings,
            ],
            'statistics' => [
                'total_order_amount' => round(floatval($totalOrderAmount), 2),
                'total_pay_amount' => round(floatval($totalPayAmount), 2),
                'total_discount_amount' => round(floatval($totalDiscountAmount), 2),
                'total_order_num' => $totalOrderNum,
            ],
            'order_list' => $orderList,
        ];
    }

    public static function getDistributionRelation($params)
    {
        $userId = intval($params['user_id'] ?? 0);
        $depth = intval($params['depth'] ?? 3);

        if ($userId <= 0) {
            return self::setError('user_id参数必填');
        }

        $user = User::findOrEmpty($userId);
        if ($user->isEmpty()) {
            return self::setError('用户不存在');
        }

        $firstLeader = $user->first_leader > 0 ? User::field('id,sn,nickname,mobile,avatar')->findOrEmpty($user->first_leader) : null;
        $secondLeader = $user->second_leader > 0 ? User::field('id,sn,nickname,mobile,avatar')->findOrEmpty($user->second_leader) : null;
        $thirdLeader = $user->third_leader > 0 ? User::field('id,sn,nickname,mobile,avatar')->findOrEmpty($user->third_leader) : null;

        $ancestorRelation = $user->ancestor_relation;
        $ancestorChain = [];
        if (!empty($ancestorRelation)) {
            $ancestorIds = array_filter(explode(',', $ancestorRelation));
            if (!empty($ancestorIds)) {
                $ancestors = User::field('id,sn,nickname,mobile')->whereIn('id', $ancestorIds)->select()->toArray();
                $ancestorMap = array_column($ancestors, null, 'id');
                foreach ($ancestorIds as $aid) {
                    if (isset($ancestorMap[$aid])) {
                        $ancestorChain[] = $ancestorMap[$aid];
                    }
                }
            }
        }

        $subordinates = self::getSubordinates($userId, min($depth, 5));

        $distribution = Distribution::where('user_id', $userId)->findOrEmpty();

        return [
            'user_info' => [
                'user_id' => $user->id,
                'nickname' => $user->nickname,
                'mobile' => $user->mobile,
                'sn' => $user->sn,
                'code' => $user->code,
                'inviter_id' => $user->inviter_id,
            ],
            'superior' => [
                'first_leader' => $firstLeader ? $firstLeader->toArray() : null,
                'second_leader' => $secondLeader ? $secondLeader->toArray() : null,
                'third_leader' => $thirdLeader ? $thirdLeader->toArray() : null,
                'ancestor_chain' => $ancestorChain,
            ],
            'subordinates' => $subordinates,
            'distribution_info' => $distribution->isEmpty() ? null : [
                'is_distribution' => $distribution->is_distribution,
                'is_distribution_desc' => $distribution->is_distribution ? '分销商' : '普通会员',
                'level_id' => $distribution->level_id,
                'is_freeze' => $distribution->is_freeze,
                'distribution_time' => $distribution->distribution_time,
            ],
        ];
    }

    private static function getSubordinates($userId, $depth, $currentDepth = 1)
    {
        if ($currentDepth > $depth) {
            return [];
        }

        $levelNames = [1 => '一级', 2 => '二级', 3 => '三级'];
        $levelName = $levelNames[$currentDepth] ?? ($currentDepth . '级');

        $directSubs = User::field('id,sn,nickname,mobile,avatar,first_leader,second_leader,create_time')
            ->where('first_leader', $userId)
            ->select()
            ->toArray();

        $result = [
            'level' => $currentDepth,
            'level_name' => $levelName,
            'count' => count($directSubs),
            'list' => [],
        ];

        foreach ($directSubs as &$sub) {
            $sub['create_time'] = self::formatTime($sub['create_time']);
            $subOrderAmount = Order::where('user_id', $sub['id'])
                ->where('pay_status', PayEnum::ISPAID)
                ->sum('order_amount');
            $sub['order_amount'] = round(floatval($subOrderAmount), 2);

            $subDistribution = Distribution::where('user_id', $sub['id'])->findOrEmpty();
            $sub['is_distribution'] = !$subDistribution->isEmpty() ? $subDistribution->is_distribution : 0;

            if ($currentDepth < $depth) {
                $sub['children'] = self::getSubordinates($sub['id'], $depth, $currentDepth + 1);
            }
            $result['list'][] = $sub;
        }

        return $result;
    }

    public static function getDistributionCommission($params)
    {
        $userId = intval($params['user_id'] ?? 0);
        $startTime = $params['start_time'] ?? '';
        $endTime = $params['end_time'] ?? '';
        $status = $params['status'] ?? '';

        if ($userId <= 0) {
            return self::setError('user_id参数必填');
        }

        $user = User::findOrEmpty($userId);
        if ($user->isEmpty()) {
            return self::setError('用户不存在');
        }

        $query = DistributionOrderGoods::where('user_id', $userId);

        if ($status !== '') {
            $query->where('status', intval($status));
        }
        if (!empty($startTime)) {
            $query->where('create_time', '>=', strtotime($startTime));
        }
        if (!empty($endTime)) {
            $query->where('create_time', '<=', strtotime($endTime . ' 23:59:59'));
        }

        $totalEarnings = (clone $query)->sum('earnings');
        $settledEarnings = DistributionOrderGoods::where('user_id', $userId)
            ->where('status', DistributionOrderGoodsEnum::RETURNED)
            ->sum('earnings');
        $unsettledEarnings = DistributionOrderGoods::where('user_id', $userId)
            ->where('status', DistributionOrderGoodsEnum::UN_RETURNED)
            ->sum('earnings');
        $invalidEarnings = DistributionOrderGoods::where('user_id', $userId)
            ->where('status', DistributionOrderGoodsEnum::EXPIRED)
            ->sum('earnings');

        $commissionList = (clone $query)
            ->field('id,user_id,order_goods_id,goods_id,earnings,level_id,level,ratio,status,create_time,settlement_time')
            ->order('id', 'desc')
            ->select()
            ->toArray();

        $levelDesc = ['自购佣金', '一级分佣', '二级分佣'];
        $statusDesc = [1 => '待返佣', 2 => '已结算', 3 => '已失效'];

        foreach ($commissionList as &$item) {
            $item['level_desc'] = $levelDesc[$item['level']] ?? '未知';
            $item['status_desc'] = $statusDesc[$item['status']] ?? '未知';
            $item['create_time'] = self::formatTime($item['create_time']);
            $item['settlement_time'] = self::formatTime($item['settlement_time']);

            $orderGoods = OrderGoods::field('goods_name,goods_price,goods_num,total_pay_price')
                ->findOrEmpty($item['order_goods_id']);
            if (!$orderGoods->isEmpty()) {
                $item['goods_name'] = $orderGoods->goods_name;
                $item['goods_price'] = $orderGoods->goods_price;
                $item['goods_num'] = $orderGoods->goods_num;
                $item['total_pay_price'] = $orderGoods->total_pay_price;
            } else {
                $item['goods_name'] = '';
                $item['goods_price'] = 0;
                $item['goods_num'] = 0;
                $item['total_pay_price'] = 0;
            }

            $buyerId = DistributionOrderGoods::alias('dog')
                ->join('order_goods og', 'og.id = dog.order_goods_id')
                ->join('order o', 'o.id = og.order_id')
                ->where('dog.id', $item['id'])
                ->value('o.user_id');
            if ($buyerId) {
                $buyer = User::field('id,nickname,mobile')->findOrEmpty($buyerId);
                $item['buyer_info'] = $buyer->isEmpty() ? null : $buyer->toArray();
            } else {
                $item['buyer_info'] = null;
            }
        }

        $distributionLevel = DistributionLevel::findOrEmpty(
            Distribution::where('user_id', $userId)->value('level_id', 0)
        );

        return [
            'user_info' => [
                'user_id' => $user->id,
                'nickname' => $user->nickname,
                'user_earnings' => $user->user_earnings,
            ],
            'statistics' => [
                'total_earnings' => round(floatval($totalEarnings), 2),
                'settled_earnings' => round(floatval($settledEarnings), 2),
                'unsettled_earnings' => round(floatval($unsettledEarnings), 2),
                'invalid_earnings' => round(floatval($invalidEarnings), 2),
            ],
            'distribution_level' => $distributionLevel->isEmpty() ? null : [
                'name' => $distributionLevel->name,
                'first_ratio' => $distributionLevel->first_ratio,
                'second_ratio' => $distributionLevel->second_ratio,
                'self_ratio' => $distributionLevel->self_ratio,
            ],
            'commission_list' => $commissionList,
        ];
    }

    public static function getFansList($params)
    {
        $userId = intval($params['user_id'] ?? 0);
        $type = $params['type'] ?? 'all';
        $keyword = $params['keyword'] ?? '';
        $pageNo = intval($params['page_no'] ?? 1);
        $pageSize = intval($params['page_size'] ?? 25);

        if ($userId <= 0) {
            return self::setError('user_id参数必填');
        }

        $user = User::findOrEmpty($userId);
        if ($user->isEmpty()) {
            return self::setError('用户不存在');
        }

        $query = User::field('id,sn,nickname,mobile,avatar,first_leader,second_leader,create_time');

        switch ($type) {
            case 'first':
                $query->where('first_leader', $userId);
                break;
            case 'second':
                $query->where('second_leader', $userId);
                break;
            default:
                $query->where('first_leader|second_leader', $userId);
        }

        if (!empty($keyword)) {
            $query->where('nickname|mobile|sn', 'like', '%' . $keyword . '%');
        }

        $total = (clone $query)->count();
        $list = (clone $query)
            ->order('id', 'desc')
            ->page($pageNo, $pageSize)
            ->select()
            ->toArray();

        foreach ($list as &$item) {
            $item['create_time'] = self::formatTime($item['create_time']);
            $item['order_amount'] = round(floatval(
                Order::where('user_id', $item['id'])
                    ->where('pay_status', PayEnum::ISPAID)
                    ->sum('order_amount')
            ), 2);
            $item['order_num'] = Order::where('user_id', $item['id'])
                ->where('pay_status', PayEnum::ISPAID)
                ->count();
            $item['is_distribution'] = Distribution::where('user_id', $item['id'])
                ->where('is_distribution', YesNoEnum::YES)
                ->count() > 0;
            $item['relation'] = $item['first_leader'] == $userId ? '一级粉丝' : '二级粉丝';
        }

        $firstCount = User::where('first_leader', $userId)->count();
        $secondCount = User::where('second_leader', $userId)->count();

        return [
            'user_info' => [
                'user_id' => $user->id,
                'nickname' => $user->nickname,
            ],
            'statistics' => [
                'total_fans' => $firstCount + $secondCount,
                'first_level_fans' => $firstCount,
                'second_level_fans' => $secondCount,
            ],
            'list' => $list,
            'page' => [
                'page_no' => $pageNo,
                'page_size' => $pageSize,
                'total' => $total,
                'pages' => ceil($total / $pageSize),
            ],
        ];
    }

    public static function getUserInfo($params)
    {
        $userId = intval($params['user_id'] ?? 0);
        $mobile = $params['mobile'] ?? '';
        $sn = $params['sn'] ?? '';

        $query = User::field('id,sn,nickname,avatar,mobile,sex,user_money,user_integral,user_earnings,total_order_amount,total_order_num,first_leader,second_leader,third_leader,code,inviter_id,register_source,create_time');

        if ($userId > 0) {
            $query->where('id', $userId);
        } elseif (!empty($mobile)) {
            $query->where('mobile', $mobile);
        } elseif (!empty($sn)) {
            $query->where('sn', $sn);
        } else {
            return self::setError('请提供user_id、mobile或sn其中一个参数');
        }

        $user = $query->findOrEmpty();
        if ($user->isEmpty()) {
            return self::setError('用户不存在');
        }

        $userData = $user->toArray();
        $userData['create_time'] = self::formatTime($userData['create_time']);

        $distribution = Distribution::where('user_id', $user->id)->findOrEmpty();
        $userData['distribution'] = $distribution->isEmpty() ? null : [
            'is_distribution' => $distribution->is_distribution,
            'level_id' => $distribution->level_id,
            'is_freeze' => $distribution->is_freeze,
            'distribution_time' => $distribution->distribution_time,
        ];

        if ($user->first_leader > 0) {
            $leader = User::field('id,sn,nickname,mobile')->findOrEmpty($user->first_leader);
            $userData['first_leader_info'] = $leader->isEmpty() ? null : $leader->toArray();
        } else {
            $userData['first_leader_info'] = null;
        }

        return $userData;
    }

    public static function getUserList($params)
    {
        $keyword = $params['keyword'] ?? '';
        $mobile = $params['mobile'] ?? '';
        $isDistribution = $params['is_distribution'] ?? '';
        $startTime = $params['start_time'] ?? '';
        $endTime = $params['end_time'] ?? '';
        $orderBy = $params['order_by'] ?? 'id';
        $orderDir = $params['order_dir'] ?? 'desc';
        $pageNo = intval($params['page_no'] ?? 1);
        $pageSize = intval($params['page_size'] ?? 25);

        if ($pageSize > 100) {
            $pageSize = 100;
        }

        $query = User::field('id,sn,nickname,avatar,mobile,sex,user_money,user_integral,user_earnings,total_order_amount,total_order_num,first_leader,code,create_time');

        if (!empty($keyword)) {
            $query->where('nickname|mobile|sn', 'like', '%' . $keyword . '%');
        }
        if (!empty($mobile)) {
            $query->where('mobile', $mobile);
        }
        if ($isDistribution !== '') {
            $distributionUserIds = Distribution::where('is_distribution', intval($isDistribution))->column('user_id');
            if (!empty($distributionUserIds)) {
                $query->whereIn('id', $distributionUserIds);
            } else {
                $query->where('id', 0);
            }
        }
        if (!empty($startTime)) {
            $query->where('create_time', '>=', strtotime($startTime));
        }
        if (!empty($endTime)) {
            $query->where('create_time', '<=', strtotime($endTime . ' 23:59:59'));
        }

        $allowedOrder = ['id', 'create_time', 'total_order_amount', 'user_money', 'user_earnings'];
        if (!in_array($orderBy, $allowedOrder)) {
            $orderBy = 'id';
        }
        $orderDir = strtolower($orderDir) === 'asc' ? 'asc' : 'desc';

        $total = (clone $query)->count();
        $list = (clone $query)
            ->order($orderBy, $orderDir)
            ->page($pageNo, $pageSize)
            ->select()
            ->toArray();

        foreach ($list as &$item) {
            $item['create_time'] = self::formatTime($item['create_time']);
            $distribution = Distribution::where('user_id', $item['id'])->findOrEmpty();
            $item['is_distribution'] = !$distribution->isEmpty() ? $distribution->is_distribution : 0;
            $item['distribution_level_id'] = !$distribution->isEmpty() ? $distribution->level_id : 0;
        }

        return [
            'statistics' => [
                'total_users' => User::count(),
                'total_distribution' => Distribution::where('is_distribution', 1)->count(),
            ],
            'list' => $list,
            'page' => [
                'page_no' => $pageNo,
                'page_size' => $pageSize,
                'total' => $total,
                'pages' => ceil($total / $pageSize),
            ],
        ];
    }

    public static function getUserValidOrderAmount($params)
    {
        $userId = intval($params['user_id'] ?? 0);
        $startTime = $params['start_time'] ?? '';
        $endTime = $params['end_time'] ?? '';

        if ($userId <= 0) {
            return self::setError('user_id参数必填');
        }

        $user = User::findOrEmpty($userId);
        if ($user->isEmpty()) {
            return self::setError('用户不存在');
        }

        $query = Order::where('user_id', $userId)
            ->where('pay_status', PayEnum::ISPAID)
            ->where('order_status', OrderEnum::STATUS_FINISH);

        if (!empty($startTime)) {
            $query->where('confirm_take_time', '>=', strtotime($startTime));
        }
        if (!empty($endTime)) {
            $query->where('confirm_take_time', '<=', strtotime($endTime . ' 23:59:59'));
        }

        $totalOrderAmount = (clone $query)->sum('order_amount');
        $totalPayAmount = (clone $query)->sum('total_amount');
        $totalOrderNum = (clone $query)->count();

        $orderList = (clone $query)
            ->field('id,sn,order_amount,total_amount,discount_amount,pay_way,confirm_take_time,create_time')
            ->order('confirm_take_time', 'desc')
            ->select()
            ->toArray();

        foreach ($orderList as &$item) {
            $item['confirm_take_time'] = self::formatTime($item['confirm_take_time']);
            $item['create_time'] = self::formatTime($item['create_time']);
        }

        $commissionTotal = DistributionOrderGoods::alias('dog')
            ->join('order_goods og', 'og.id = dog.order_goods_id')
            ->join('order o', 'o.id = og.order_id')
            ->where('o.user_id', $userId)
            ->where('o.pay_status', PayEnum::ISPAID)
            ->where('o.order_status', OrderEnum::STATUS_FINISH)
            ->where('dog.status', DistributionOrderGoodsEnum::RETURNED)
            ->sum('dog.earnings');

        return [
            'user_info' => [
                'user_id' => $user->id,
                'nickname' => $user->nickname,
                'mobile' => $user->mobile,
                'sn' => $user->sn,
            ],
            'statistics' => [
                'valid_order_amount' => round(floatval($totalOrderAmount), 2),
                'valid_pay_amount' => round(floatval($totalPayAmount), 2),
                'valid_order_num' => $totalOrderNum,
                'generated_commission' => round(floatval($commissionTotal), 2),
            ],
            'order_list' => $orderList,
        ];
    }

    public static function getUserCommissionStats($params)
    {
        $userId = intval($params['user_id'] ?? 0);
        $startTime = $params['start_time'] ?? '';
        $endTime = $params['end_time'] ?? '';

        if ($userId <= 0) {
            return self::setError('user_id参数必填');
        }

        $user = User::findOrEmpty($userId);
        if ($user->isEmpty()) {
            return self::setError('用户不存在');
        }

        $distribution = Distribution::where('user_id', $userId)->findOrEmpty();
        if ($distribution->isEmpty() || !$distribution->is_distribution) {
            return self::setError('该用户不是分销商');
        }

        $query = DistributionOrderGoods::where('user_id', $userId);

        if (!empty($startTime)) {
            $query->where('create_time', '>=', strtotime($startTime));
        }
        if (!empty($endTime)) {
            $query->where('create_time', '<=', strtotime($endTime . ' 23:59:59'));
        }

        $totalEarnings = (clone $query)->sum('earnings');
        $settledEarnings = (clone $query)->where('status', DistributionOrderGoodsEnum::RETURNED)->sum('earnings');
        $unsettledEarnings = (clone $query)->where('status', DistributionOrderGoodsEnum::UN_RETURNED)->sum('earnings');
        $invalidEarnings = (clone $query)->where('status', DistributionOrderGoodsEnum::EXPIRED)->sum('earnings');

        $settledFromValid = DistributionOrderGoods::alias('dog')
            ->join('order_goods og', 'og.id = dog.order_goods_id')
            ->join('order o', 'o.id = og.order_id')
            ->where('dog.user_id', $userId)
            ->where('dog.status', DistributionOrderGoodsEnum::RETURNED)
            ->where('o.order_status', OrderEnum::STATUS_FINISH)
            ->sum('dog.earnings');

        $firstLevelEarnings = (clone $query)->where('level', 1)->sum('earnings');
        $secondLevelEarnings = (clone $query)->where('level', 2)->sum('earnings');
        $selfPurchaseEarnings = (clone $query)->where('level', 0)->sum('earnings');

        $levelInfo = null;
        if ($distribution->level_id > 0) {
            $level = DistributionLevel::findOrEmpty($distribution->level_id);
            if (!$level->isEmpty()) {
                $levelInfo = [
                    'name' => $level->name,
                    'first_ratio' => $level->first_ratio,
                    'second_ratio' => $level->second_ratio,
                    'self_ratio' => $level->self_ratio,
                ];
            }
        }

        $fansCount = User::where('first_leader', $userId)->count();
        $secondFansCount = User::where('second_leader', $userId)->count();
        $fansOrderAmount = Order::alias('o')
            ->join('user u', 'u.id = o.user_id')
            ->where('u.first_leader', $userId)
            ->where('o.pay_status', PayEnum::ISPAID)
            ->where('o.order_status', OrderEnum::STATUS_FINISH)
            ->sum('o.order_amount');

        return [
            'user_info' => [
                'user_id' => $user->id,
                'nickname' => $user->nickname,
                'mobile' => $user->mobile,
                'sn' => $user->sn,
                'user_earnings' => $user->user_earnings,
                'user_money' => $user->user_money,
            ],
            'distribution_info' => [
                'is_distribution' => $distribution->is_distribution,
                'level_id' => $distribution->level_id,
                'level_info' => $levelInfo,
                'is_freeze' => $distribution->is_freeze,
                'distribution_time' => self::formatTime($distribution->distribution_time),
            ],
            'earnings_statistics' => [
                'total_earnings' => round(floatval($totalEarnings), 2),
                'settled_earnings' => round(floatval($settledEarnings), 2),
                'unsettled_earnings' => round(floatval($unsettledEarnings), 2),
                'invalid_earnings' => round(floatval($invalidEarnings), 2),
                'settled_from_valid_orders' => round(floatval($settledFromValid), 2),
            ],
            'level_earnings' => [
                'first_level' => round(floatval($firstLevelEarnings), 2),
                'second_level' => round(floatval($secondLevelEarnings), 2),
                'self_purchase' => round(floatval($selfPurchaseEarnings), 2),
            ],
            'fans_statistics' => [
                'first_level_count' => $fansCount,
                'second_level_count' => $secondFansCount,
                'total_count' => $fansCount + $secondFansCount,
                'fans_valid_order_amount' => round(floatval($fansOrderAmount), 2),
            ],
        ];
    }

    public static function getRefundList($params)
    {
        $userId = intval($params['user_id'] ?? 0);
        $status = $params['status'] ?? '';
        $refundType = $params['refund_type'] ?? '';
        $refundMethod = $params['refund_method'] ?? '';
        $startTime = $params['start_time'] ?? '';
        $endTime = $params['end_time'] ?? '';
        $pageNo = intval($params['page_no'] ?? 1);
        $pageSize = intval($params['page_size'] ?? 25);

        if ($pageSize > 100) {
            $pageSize = 100;
        }

        $query = AfterSale::alias('as')
            ->field('as.id,as.sn,as.user_id,as.order_id,as.refund_type,as.refund_method,as.refund_total_amount,as.refund_way,as.status,as.sub_status,as.create_time,as.update_time')
            ->order('as.id', 'desc');

        if ($userId > 0) {
            $query->where('as.user_id', $userId);
        }
        if ($status !== '') {
            $query->where('as.status', intval($status));
        }
        if ($refundType !== '') {
            $query->where('as.refund_type', intval($refundType));
        }
        if ($refundMethod !== '') {
            $query->where('as.refund_method', intval($refundMethod));
        }
        if (!empty($startTime)) {
            $query->where('as.create_time', '>=', strtotime($startTime));
        }
        if (!empty($endTime)) {
            $query->where('as.create_time', '<=', strtotime($endTime . ' 23:59:59'));
        }

        $total = (clone $query)->count();
        $list = (clone $query)
            ->page($pageNo, $pageSize)
            ->select()
            ->toArray();

        $refundTypeDesc = [1 => '整单退款', 2 => '商品售后'];
        $refundMethodDesc = [1 => '仅退款', 2 => '退货退款'];
        $refundWayDesc = [1 => '原路退回', 2 => '退回余额'];

        foreach ($list as &$item) {
            $item['refund_type_desc'] = $refundTypeDesc[$item['refund_type']] ?? '';
            $item['refund_method_desc'] = $refundMethodDesc[$item['refund_method']] ?? '';
            $item['refund_way_desc'] = $refundWayDesc[$item['refund_way']] ?? '';
            $item['status_desc'] = AfterSaleEnum::getStatusDesc($item['status']);
            $item['sub_status_desc'] = AfterSaleEnum::getSubStatusDesc($item['sub_status']);
            $item['create_time'] = self::formatTime($item['create_time']);
            $item['update_time'] = self::formatTime($item['update_time']);

            $user = User::field('id,nickname,mobile,sn')->findOrEmpty($item['user_id']);
            $item['user_info'] = $user->isEmpty() ? null : $user->toArray();

            $order = Order::field('id,sn,order_amount')->findOrEmpty($item['order_id']);
            $item['order_info'] = $order->isEmpty() ? null : $order->toArray();

            $goodsList = AfterSaleGoods::alias('asg')
                ->field('asg.id,asg.goods_id,asg.goods_num,asg.goods_price,asg.refund_amount')
                ->where('asg.after_sale_id', $item['id'])
                ->select()
                ->toArray();
            foreach ($goodsList as &$goods) {
                $orderGoods = OrderGoods::field('goods_name,goods_snap')->findOrEmpty(
                    AfterSaleGoods::where('id', $goods['id'])->value('order_goods_id', 0)
                );
                $goods['goods_name'] = $orderGoods->isEmpty() ? '' : $orderGoods->goods_name;
            }
            $item['goods_list'] = $goodsList;
        }

        $totalRefundAmount = AfterSale::where('status', AfterSaleEnum::STATUS_SUCCESS)->sum('refund_total_amount');

        return [
            'statistics' => [
                'total_refund_count' => AfterSale::count(),
                'success_refund_count' => AfterSale::where('status', AfterSaleEnum::STATUS_SUCCESS)->count(),
                'ing_refund_count' => AfterSale::where('status', AfterSaleEnum::STATUS_ING)->count(),
                'fail_refund_count' => AfterSale::where('status', AfterSaleEnum::STATUS_FAIL)->count(),
                'total_refund_amount' => round(floatval($totalRefundAmount), 2),
            ],
            'list' => $list,
            'page' => [
                'page_no' => $pageNo,
                'page_size' => $pageSize,
                'total' => $total,
                'pages' => ceil($total / $pageSize),
            ],
        ];
    }

    public static function getRefundDetail($params)
    {
        $refundId = intval($params['refund_id'] ?? 0);
        $sn = $params['sn'] ?? '';

        $query = AfterSale::alias('as');

        if ($refundId > 0) {
            $query->where('as.id', $refundId);
        } elseif (!empty($sn)) {
            $query->where('as.sn', $sn);
        } else {
            return self::setError('请提供refund_id或sn参数');
        }

        $afterSale = $query->find();
        if (empty($afterSale)) {
            return self::setError('退款订单不存在');
        }

        $refundTypeDesc = [1 => '整单退款', 2 => '商品售后'];
        $refundMethodDesc = [1 => '仅退款', 2 => '退货退款'];
        $refundWayDesc = [1 => '原路退回', 2 => '退回余额'];

        $data = $afterSale->toArray();
        $data['refund_type_desc'] = $refundTypeDesc[$data['refund_type']] ?? '';
        $data['refund_method_desc'] = $refundMethodDesc[$data['refund_method']] ?? '';
        $data['refund_way_desc'] = $refundWayDesc[$data['refund_way']] ?? '';
        $data['status_desc'] = AfterSaleEnum::getStatusDesc($data['status']);
        $data['sub_status_desc'] = AfterSaleEnum::getSubStatusDesc($data['sub_status']);
        $data['create_time'] = self::formatTime($data['create_time']);
        $data['update_time'] = self::formatTime($data['update_time']);
        $data['audit_time'] = self::formatTime($data['audit_time']);
        $data['express_time'] = self::formatTime($data['express_time']);
        $data['confirm_take_time'] = self::formatTime($data['confirm_take_time']);

        $user = User::field('id,sn,nickname,mobile,avatar')->findOrEmpty($data['user_id']);
        $data['user_info'] = $user->isEmpty() ? null : $user->toArray();

        $order = Order::field('id,sn,order_amount,total_amount,order_status')->findOrEmpty($data['order_id']);
        $data['order_info'] = $order->isEmpty() ? null : $order->toArray();

        $goodsList = AfterSaleGoods::alias('asg')
            ->field('asg.id,asg.order_goods_id,asg.goods_id,asg.goods_num,asg.goods_price,asg.refund_amount')
            ->where('asg.after_sale_id', $data['id'])
            ->select()
            ->toArray();
        foreach ($goodsList as &$goods) {
            $orderGoods = OrderGoods::field('goods_name,goods_price,goods_num,total_pay_price,goods_snap')->findOrEmpty($goods['order_goods_id']);
            $goods['goods_name'] = $orderGoods->isEmpty() ? '' : $orderGoods->goods_name;
            $goods['original_price'] = $orderGoods->isEmpty() ? 0 : $orderGoods->goods_price;
            $goods['original_num'] = $orderGoods->isEmpty() ? 0 : $orderGoods->goods_num;
        }
        $data['goods_list'] = $goodsList;

        return $data;
    }

    public static function getUserRefundStatistics($params)
    {
        $userId = intval($params['user_id'] ?? 0);
        $startTime = $params['start_time'] ?? '';
        $endTime = $params['end_time'] ?? '';

        if ($userId <= 0) {
            return self::setError('user_id参数必填');
        }

        $user = User::findOrEmpty($userId);
        if ($user->isEmpty()) {
            return self::setError('用户不存在');
        }

        $query = AfterSale::where('user_id', $userId);

        if (!empty($startTime)) {
            $query->where('create_time', '>=', strtotime($startTime));
        }
        if (!empty($endTime)) {
            $query->where('create_time', '<=', strtotime($endTime . ' 23:59:59'));
        }

        $totalRefundCount = (clone $query)->count();
        $successRefundCount = (clone $query)->where('status', AfterSaleEnum::STATUS_SUCCESS)->count();
        $ingRefundCount = (clone $query)->where('status', AfterSaleEnum::STATUS_ING)->count();
        $failRefundCount = (clone $query)->where('status', AfterSaleEnum::STATUS_FAIL)->count();

        $totalRefundAmount = (clone $query)->where('status', AfterSaleEnum::STATUS_SUCCESS)->sum('refund_total_amount');
        $onlyRefundCount = (clone $query)->where('refund_method', AfterSaleEnum::METHOD_ONLY_REFUND)->count();
        $returnGoodsCount = (clone $query)->where('refund_method', AfterSaleEnum::METHOD_REFUND_GOODS)->count();

        $recentRefunds = (clone $query)
            ->field('id,sn,order_id,refund_total_amount,status,sub_status,create_time')
            ->order('id', 'desc')
            ->limit(10)
            ->select()
            ->toArray();

        foreach ($recentRefunds as &$item) {
            $item['status_desc'] = AfterSaleEnum::getStatusDesc($item['status']);
            $item['create_time'] = self::formatTime($item['create_time']);
            $order = Order::field('id,sn,order_amount')->findOrEmpty($item['order_id']);
            $item['order_info'] = $order->isEmpty() ? null : [
                'id' => $order->id,
                'sn' => $order->sn,
                'order_amount' => $order->order_amount,
            ];
        }

        return [
            'user_info' => [
                'user_id' => $user->id,
                'nickname' => $user->nickname,
                'mobile' => $user->mobile,
                'sn' => $user->sn,
            ],
            'statistics' => [
                'total_refund_count' => $totalRefundCount,
                'success_refund_count' => $successRefundCount,
                'ing_refund_count' => $ingRefundCount,
                'fail_refund_count' => $failRefundCount,
                'total_refund_amount' => round(floatval($totalRefundAmount), 2),
                'only_refund_count' => $onlyRefundCount,
                'return_goods_count' => $returnGoodsCount,
            ],
            'recent_refunds' => $recentRefunds,
        ];
    }

    public static function getUserOrderList($params)
    {
        $userId = intval($params['user_id'] ?? 0);
        $orderStatus = $params['order_status'] ?? '';
        $startTime = $params['start_time'] ?? '';
        $endTime = $params['end_time'] ?? '';
        $pageNo = intval($params['page_no'] ?? 1);
        $pageSize = intval($params['page_size'] ?? 25);

        if ($pageSize > 100) {
            $pageSize = 100;
        }

        if ($userId <= 0) {
            return self::setError('user_id参数必填');
        }

        $query = Order::alias('o')
            ->field('o.id,o.sn,o.user_id,o.order_status,o.order_amount,o.total_amount,o.total_pay_price,o.delivery_time,o.confirm_take_time,o.create_time')
            ->order('o.id', 'desc');

        $query->where('o.user_id', $userId);

        if ($orderStatus !== '') {
            $query->where('o.order_status', intval($orderStatus));
        }
        if (!empty($startTime)) {
            $query->where('o.create_time', '>=', strtotime($startTime));
        }
        if (!empty($endTime)) {
            $query->where('o.create_time', '<=', strtotime($endTime . ' 23:59:59'));
        }

        $total = (clone $query)->count();
        $list = (clone $query)
            ->page($pageNo, $pageSize)
            ->select()
            ->toArray();

        $orderStatusDesc = OrderEnum::getStatusDesc(null, true);

        foreach ($list as &$item) {
            $item['order_status_desc'] = $orderStatusDesc[$item['order_status']] ?? '';
            $item['create_time'] = self::formatTime($item['create_time']);
            $item['delivery_time'] = self::formatTime($item['delivery_time']);
            $item['confirm_take_time'] = self::formatTime($item['confirm_take_time']);

            $orderGoods = OrderGoods::field('id,goods_name,goods_price,goods_num,total_pay_price,goods_snap')
                ->where('order_id', $item['id'])
                ->select()
                ->toArray();
            foreach ($orderGoods as &$goods) {
                if (!empty($goods['goods_snap'])) {
                    $snap = json_decode($goods['goods_snap'], true);
                    $goods['goods_image'] = $snap['image'] ?? '';
                }
            }
            $item['goods_list'] = $orderGoods;
        }

        $totalAmount = (clone $query)->sum('order_amount');
        $totalPayAmount = (clone $query)->sum('total_pay_price');

        return [
            'statistics' => [
                'total_count' => Order::where('user_id', $userId)->count(),
                'total_order_amount' => round(floatval($totalAmount), 2),
                'total_pay_amount' => round(floatval($totalPayAmount), 2),
                'pending_pay_count' => Order::where('user_id', $userId)->where('order_status', OrderEnum::STATUS_WAIT_PAY)->count(),
                'pending_delivery_count' => Order::where('user_id', $userId)->where('order_status', OrderEnum::STATUS_WAIT_DELIVERY)->count(),
                'pending_take_count' => Order::where('user_id', $userId)->where('order_status', OrderEnum::STATUS_WAIT_TAKE)->count(),
                'completed_count' => Order::where('user_id', $userId)->where('order_status', OrderEnum::STATUS_COMPLETE)->count(),
                'closed_count' => Order::where('user_id', $userId)->where('order_status', OrderEnum::STATUS_CLOSE)->count(),
            ],
            'list' => $list,
            'page' => [
                'page_no' => $pageNo,
                'page_size' => $pageSize,
                'total' => $total,
                'pages' => ceil($total / $pageSize),
            ],
        ];
    }

    public static function getRechargeCommission($params)
    {
        $userId = intval($params['user_id'] ?? 0);
        $startTime = $params['start_time'] ?? '';
        $endTime = $params['end_time'] ?? '';
        $pageNo = intval($params['page_no'] ?? 1);
        $pageSize = intval($params['page_size'] ?? 25);

        if ($userId <= 0) {
            return self::setError('user_id参数必填');
        }

        $user = User::findOrEmpty($userId);
        if ($user->isEmpty()) {
            return self::setError('用户不存在');
        }

        $query = RechargeCommission::where('user_id', $userId);

        if (!empty($startTime)) {
            $query->where('create_time', '>=', strtotime($startTime));
        }
        if (!empty($endTime)) {
            $query->where('create_time', '<=', strtotime($endTime . ' 23:59:59'));
        }

        $total = (clone $query)->count();
        $totalEarnings = (clone $query)->sum('earnings');
        $firstEarnings = (clone $query)->where('level', 1)->sum('earnings');
        $secondEarnings = (clone $query)->where('level', 2)->sum('earnings');

        $list = (clone $query)
            ->field('id,sn,recharge_order_sn,from_user_id,level,ratio,recharge_amount,earnings,status,settle_time,create_time')
            ->order('id', 'desc')
            ->page($pageNo, $pageSize)
            ->select()
            ->toArray();

        foreach ($list as &$item) {
            $item['level_desc'] = $item['level'] == 1 ? '一级' : '二级';
            $item['status_desc'] = '已结算';
            $item['settle_time'] = self::formatTime($item['settle_time']);
            $item['create_time'] = self::formatTime($item['create_time']);

            $fromUser = User::findOrEmpty($item['from_user_id']);
            $item['from_user_info'] = $fromUser->isEmpty() ? null : [
                'id' => $fromUser->id,
                'nickname' => $fromUser->nickname,
                'mobile' => $fromUser->mobile,
            ];
            unset($item['from_user_id']);
        }

        return [
            'user_info' => [
                'user_id' => $userId,
                'nickname' => $user->nickname,
                'user_earnings' => $user->user_earnings,
            ],
            'statistics' => [
                'total_earnings' => $totalEarnings,
                'first_level_earnings' => $firstEarnings,
                'second_level_earnings' => $secondEarnings,
                'total_count' => $total,
            ],
            'list' => $list,
            'page' => [
                'page_no' => $pageNo,
                'page_size' => $pageSize,
                'total' => $total,
                'pages' => ceil($total / $pageSize),
            ],
        ];
    }
}
