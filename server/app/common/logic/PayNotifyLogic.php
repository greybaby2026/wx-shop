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

namespace app\common\logic;


use app\adminapi\logic\distribution\DistributionLevelLogic;
use app\common\cache\HandleConcurrencyCache;
use app\common\cache\YlyPrinterCache;
use app\common\enum\AccountLogEnum;
use app\common\enum\DeliveryEnum;
use app\common\enum\FootprintEnum;
use app\common\enum\IntegralGoodsEnum;
use app\common\enum\IntegralOrderEnum;
use app\common\enum\NoticeEnum;
use app\common\enum\OrderEnum;
use app\common\enum\OrderLogEnum;
use app\common\enum\PayEnum;
use app\common\enum\PrinterEnum;
use app\common\enum\TeamEnum;
use app\common\model\IntegralGoods;
use app\common\model\IntegralOrder;
use app\common\model\Order;
use app\common\model\OrderGoods;
use app\common\model\OrderLog;
use app\common\model\Presell;
use app\common\model\PresellGoodsItem;
use app\common\model\RechargeOrder;
use app\common\model\SeckillGoodsItem;
use app\common\model\SelffetchShop;
use app\common\model\TeamActivity;
use app\common\model\TeamFound;
use app\common\model\TeamGoods;
use app\common\model\TeamGoodsItem;
use app\common\model\TeamJoin;
use app\common\model\User;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\printer\YlyPrinterService;
use app\shopapi\logic\TeamLogic;
use think\facade\Db;
use think\facade\Log;
use app\common\logic\RechargeCommissionLogic;

/**
 * 支付成功后处理订单状态
 * Class PayNotifyLogic
 * @package app\api\logic
 */
class PayNotifyLogic extends BaseLogic
{
    public static function handle($action, $orderSn, $extra = [])
    {
        //使用redis分布式锁控制并发
        $lockKey = 'pay_notify_' . $orderSn;
        $lockValue = uniqid(); // 生成唯一标识
        $redis = (new HandleConcurrencyCache())->getRedis();

        Db::startTrans();
        try {
            // 尝试加锁
            if ($redis !== false && !$redis->set($lockKey, $lockValue, ['NX', 'EX' => 60])) {
                Log::write('订单' . $orderSn . '正在处理中，跳过重复回调', 'PayNotifyLogic');
                return false;//订单处理中，直接返回
            }

            self::$action($orderSn, $extra);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            Log::write($e->__toString(), 'PayNotifyLogic');
            self::setError($e->getMessage());
            return $e->getMessage();
        } finally {
            if ($redis !== false && $redis->get($lockKey) === $lockValue) {
                // 释放锁
                $redis->del($lockKey);
            }
        }
    }


    //下单回调 //调用回调方法统一处理 更新订单支付状态
    private static function order($orderSn, $extra = [])
    {
        $order = Order::with(['order_goods'])->where(['sn' => $orderSn])
            ->lock(true)  // 添加行锁
            ->findOrEmpty();

        // 检查订单状态
        if ($order['order_status'] != OrderEnum::STATUS_WAIT_PAY || $order['pay_status'] != PayEnum::UNPAID) {
            return false;
        }

        // 汽泡足迹
        event('Footprint', ['type' => FootprintEnum::ORDER_SETTLEMENT, 'user_id' => $order['user_id']]);

        //增加用户累计消费额度
        User::where(['id' => $order['user_id']])
            ->inc('total_order_amount', $order['order_amount'])
            ->inc('total_order_num')
            ->update();


        //赠送积分
        $open_award = ConfigService::get('award_integral', 'open_award', 0);
        if ($open_award == 1) {
            $award_event = ConfigService::get('award_integral', 'award_event', 0);
            $award_ratio = ConfigService::get('award_integral', 'award_ratio', 0);
            if ($award_ratio > 0) {
                $award_integral = floor($order['order_amount'] * ($award_ratio / 100));
            }
        }

        //更新订单状态
        Order::update([
            'pay_status'            => PayEnum::ISPAID,
            'pay_time'              => time(),
            // 自提变为待收货
            'order_status'          => $order['delivery_type'] == DeliveryEnum::SELF_DELIVERY ? OrderEnum::STATUS_WAIT_RECEIVE: OrderEnum::STATUS_WAIT_DELIVERY,
            'transaction_id'        => $extra['transaction_id'] ?? '',
            'award_integral_event'  => $award_event ?? 0,
            'award_integral'        => $award_integral ?? 0
        ], ['id' => $order['id']]);

        // 秒杀订单更新销售数据
        if ($order['order_type'] == OrderEnum::SECKILL_ORDER) {
            $orderGoods = OrderGoods::where('order_id', $order['id'])->findOrEmpty()->toArray();
            if (empty($orderGoods)) {
                return false;
            }
            $seckillGoodsItem = SeckillGoodsItem::where([
                'seckill_id' => $order['seckill_id'],
                'item_id' => $orderGoods['item_id'],
            ])->findOrEmpty();
            if ($seckillGoodsItem->isEmpty()) {
                return false;
            }
            $seckillGoodsItem->sales_amount += $orderGoods['total_pay_price'];
            $seckillGoodsItem->sales_volume += $orderGoods['goods_num'];
            $seckillGoodsItem->closing_order ++;
            $seckillGoodsItem->save();
        }

        // 拼团订单数据更新
//        if ($order['order_type'] == OrderEnum::TEAM_ORDER) {
//            if (empty($order['order_goods'])) {
//                return false;
//            }
//
//            $teamId = (new TeamFound())->where(['id'=>$order['team_found_id']])->value('team_id');
//            TeamGoodsItem::update([
//                'sales_amount'  => ['inc', $order['order_amount']],
//                'sales_volume'  => ['inc', $order['total_num']],
//                'closing_order' => ['inc', 1]
//            ], ['team_id'=>$teamId, 'item_id'=>$order['order_goods'][0]['item_id']]);
//        }
        
        // 预售订单数据更新
        if ($order['order_type'] == OrderEnum::PRESELL_ORDER) {
            PresellGoodsItem::update([
                'sale_order'    => Db::raw('sale_order+1'),
                'sale_money'    => Db::raw('sale_money+' . $order['order_amount']),
                'sale_nums'     => Db::raw('sale_nums+' . $order['total_num']),
            ], [
                [ 'item_id' , '=', $order['order_goods'][0]['item_id'] ],
                [ 'presell_id' , '=', $order['presell_id'] ],
            ]);
            Presell::update([
                'sale_order'    => Db::raw('sale_order+1'),
                'sale_money'    => Db::raw('sale_money+' . $order['order_amount']),
                'sale_nums'     => Db::raw('sale_nums+' . $order['total_num']),
            ], [ [ 'id' , '=', $order['presell_id'] ] ]);
        }

        // 生成分销订单
        DistributionOrderGoodsLogic::add($order['id']);
        // 更新分销商等级
        DistributionLevelLogic::updateDistributionLevel($order['user_id']);

        //下架库存为零的商品
//        $goods_ids = OrderGoods::where('order_id',$order['id'])->column('goods_id');
//        Goods::update(['status'=>0],['id'=>$goods_ids,'total_stock'=>0]);

        //订单日志
        (new OrderLog())->record([
            'type' => OrderLogEnum::TYPE_USER,
            'channel' => OrderLogEnum::USER_PAID_ORDER,
            'order_id' => $order['id'],
            'operator_id' => $order['user_id']
        ]);

        // 如果是拼团订单
        if ($order['order_type'] == OrderEnum::TEAM_ORDER) {
//            TeamLogic::checkTeamSuccess($order['team_found_id']);

            if (empty($order['order_goods'])) {
                return false;
            }

            $time = time();
            $teamFoundId = $order['team_found_id'];
            $teamJoinIdentity = 2;
            $teamActivity = TeamActivity::findOrEmpty($order['team_activity_id'])->toArray();
            $teamFound = (new TeamFound())->findOrEmpty($order['team_found_id'])->toArray();
            $payTeamCount = !empty($teamFound) ? ($teamFound['join'] + 1) : 1;
            $teamGoods = TeamGoods::where(['team_id' => $order['team_activity_id'], 'goods_id' => $order['order_goods'][0]['goods_id']])->findOrEmpty()->toArray();
            $teamGoodsItem = TeamGoodsItem::where(['team_id' => $order['team_activity_id'], 'item_id' => $order['order_goods'][0]['item_id']])->findOrEmpty()->toArray();
            $goodsInfo = [
                'id' => intval($order['order_goods'][0]['goods_id']),
                'item_id' => intval($order['order_goods'][0]['item_id']),
                'spec_value_ids' => $order['order_goods'][0]['spec_value_ids'],
                'name' => $teamGoods['goods_snap']['name'],
                'image' => $teamGoodsItem['item_snap']['image'] ? FileService::getFileUrl($teamGoodsItem['item_snap']['image']) : FileService::getFileUrl($teamGoods['goods_snap']['image']),
                'spec_value_str' => $teamGoodsItem['item_snap']['spec_value_str'],
                'cost_price' => $teamGoodsItem['sell_price'],
                'sell_price' => $teamGoodsItem['team_price'],
                'total_price' => round($teamGoodsItem['team_price'] * $order['order_goods'][0]['goods_num'], 2),
                'count' => intval($order['order_goods'][0]['goods_num']),
                'goods_snap' => $teamGoods['goods_snap'],
            ];

            //检测到该团的坑位已满 或 检测到该团已结束，则开一个新团并且此人做团长。
            if (empty($teamFound) || $teamFound['people'] == $teamFound['join']) {
                // 开团
                $found = TeamFound::create([
                    'found_sn' => generate_sn((new TeamFound()), 'found_sn'),
                    'team_id' => $order['team_activity_id'],
                    'user_id' => $order['user_id'],
                    'order_id' => $order['id'],
                    'people' => $teamActivity['people_num'],
                    'join' => 1,
                    'status' => 0,
                    'goods_snap' => json_encode([
                        'id' => $order['order_goods'][0]['goods_id'],
                        'name' => $order['order_goods'][0]['goods_name'],
                        'image' => $goodsInfo['image']
                    ], JSON_UNESCAPED_UNICODE),
                    'kaituan_time' => $time,
                    'invalid_time' => ($teamActivity['effective_time'] * 60) + time()
                ]);
                $teamFoundId = $found->id;
                $teamJoinIdentity = 1;
            }
            // 参团
            TeamJoin::create([
                'join_sn' => generate_sn((new TeamJoin()), 'join_sn'),
                'team_id' => $order['team_activity_id'],
                'found_id' => $teamFoundId,
                'identity' => $teamJoinIdentity,
                'user_id' => $order['user_id'],
                'order_id' => $order['id'],
                'status' => 0,
                'team_snap' => json_encode($teamActivity, JSON_UNESCAPED_UNICODE),
                'goods_snap' => json_encode($goodsInfo, JSON_UNESCAPED_UNICODE),
                'invalid_time' => ($teamActivity['effective_time'] * 60) + time(),
                'create_time' => $time,
                'update_time' => $time
            ]);

            // 更新数据
            TeamActivity::update(['partake_number' => ['inc', 1]], ['id' => $teamActivity['id']]);
            TeamFound::update(['join' => $payTeamCount], ['id' => $teamFoundId]);
            Order::update(['team_found_id' => $teamFoundId], ['id' => $order['id']]);
            TeamGoodsItem::update([
                'sales_amount'  => ['inc', $order['order_amount']],
                'sales_volume'  => ['inc', $order['total_num']],
                'closing_order' => ['inc', 1]
            ], ['team_id'=>$teamActivity['id'], 'item_id'=>$order['order_goods'][0]['item_id']]);

            // 满足条件: 拼团成功
            if ($payTeamCount == $teamActivity['people_num']) {
                TeamFound::update([
                    'status' => TeamEnum::TEAM_FOUND_SUCCESS,
                    'team_end_time' => $time
                ], ['id' => $teamFoundId]);

                $teamJoin = (new TeamJoin())->field('id,order_id')
                    ->where(['found_id' => $teamFoundId])
                    ->select()
                    ->toArray();
                foreach ($teamJoin as $item) {
                    // 标记拼团状态为成功
                    TeamJoin::update([
                        'status' => TeamEnum::TEAM_FOUND_SUCCESS,
                        'team_end_time' => $time,
                        'update_time' => $time
                    ], ['id' => $item['id']]);

                    // 标记拼团订单状态成功
                    Order::update([
                        'is_team_success' => 1,
                        'update_time' => $time
                    ], ['id' => $item['order_id']]);
                }
            }
        }

        // 消息通知 - 通知买家
        event('Notice', [
            'scene_id' =>  NoticeEnum::ORDER_PAY_NOTICE,
            'params' => [
                'user_id' => $order['user_id'],
                'order_id' => $order['id'],
                'mobile'    => $order['address']->mobile,
            ]
        ]);

        // 消息通知 - 通知卖家
        $mobile = ConfigService::get('shop', 'mall_contact_mobile', '');
        event('Notice', [
            'scene_id' =>  NoticeEnum::SELLER_ORDER_PAY_NOTICE,
            'params' => [
                'mobile' => $mobile,
                'order_id' => $order['id'],
            ]
        ]);
        //更新虚拟订单
        GoodsVirtualLogic::afterPayVirtualDelivery($order['id']);
        //更新用户等级
        UserLogic::updateLevel($order['user_id']);

        // 自动小票打印
        self::orderPrint($order['id']);
    }

    /**
     * @notes 充值成功回调
     * @param $orderSn
     * @param array $extra
     * @author Tab
     * @date 2021/8/11 14:43
     */
    public static function recharge($orderSn, $extra = [])
    {
        $order = RechargeOrder::where('sn', $orderSn)->findOrEmpty();
        // 增加用户累计充值金额及用户余额
        $user = User::findOrEmpty($order->user_id);
        $user->total_recharge_amount = $user->total_recharge_amount + $order->order_amount;
        $user->user_money = $user->user_money + $order->order_amount;
        $user->save();

        // 记录账户流水
        AccountLogLogic::add($order->user_id, AccountLogEnum::BNW_INC_RECHARGE, AccountLogEnum::INC, $order->order_amount, $order->sn, '用户充值');

        // 更新充值订单状态
        $order->transaction_id = $extra['transaction_id'];
        $order->pay_status = PayEnum::ISPAID;
        $order->pay_time = time();
        $order->save();

        // 充值奖励
        foreach($order->award as $item) {
            if(isset($item['give_money']) && $item['give_money'] > 0) {
                // 充值送余额
                self::awardMoney($order, $item['give_money']);
            }
        }

        // 充值分销佣金结算
        RechargeCommissionLogic::settle($order);

    }

    /**
     * @notes 充值送余额
     * @param $userId
     * @param $giveMoney
     * @author Tab
     * @date 2021/8/11 14:35
     */
    public static function awardMoney($order, $giveMoney)
    {
        // 充值送余额
        $user = User::findOrEmpty($order->user_id);
        $user->user_money = $user->user_money + $giveMoney;
        $user->save();
        // 记录账户流水
        AccountLogLogic::add($order->user_id, AccountLogEnum::BNW_INC_RECHARGE_GIVE, AccountLogEnum::INC, $giveMoney, $order->sn, '充值赠送');
    }


    /**
     * @notes 积分商城订单
     * @param $order_sn
     * @param array $extra
     * @author 段誉
     * @date 2022/3/31 12:13
     */
    private static function integral($order_sn, $extra = [])
    {
        $order = IntegralOrder::where(['sn' => $order_sn])->findOrEmpty();
        $goods = $order['goods_snap'];

        // 更新订单状态
        $data = [
            'order_status' => IntegralOrderEnum::ORDER_STATUS_DELIVERY,
            'pay_status' => PayEnum::ISPAID,
            'pay_time' => time(),
        ];
        // 红包类型 或者 无需物流 支付完即订单完成
        if ($goods['type'] == IntegralGoodsEnum::TYPE_BALANCE || $goods['delivery_way'] == IntegralGoodsEnum::DELIVERY_NO_EXPRESS) {
            $data['order_status'] = IntegralOrderEnum::ORDER_STATUS_COMPLETE;
            $data['confirm_time'] = time();
        }
        // 第三方流水号
        if (isset($extra['transaction_id'])) {
            $data['transaction_id'] = $extra['transaction_id'];
        }
        IntegralOrder::update($data, ['id' => $order['id']]);

        // 更新商品销量
        IntegralGoods::where([['id', '=', $goods['id']], ['stock', '>=', $order['total_num']]])
            ->dec('stock', $order['total_num'])
            ->inc('sales', $order['total_num'])
            ->update();

        // 红包类型，直接增加余额
        if ($goods['type'] == IntegralGoodsEnum::TYPE_BALANCE) {
            $reward = round($goods['balance'] * $order['total_num'], 2);
            User::where(['id' => $order['user_id']])
                ->inc('user_money', $reward)
                ->update();

            AccountLogLogic::add(
                $order['user_id'],
                AccountLogEnum::BNW_INC_INTEGRAL_ORDER,
                AccountLogEnum::INC, $reward, $order['sn']
            );
        }
    }


    /**
     * @notes 小票打印
     * @param $orderId
     * @author Tab
     * @date 2021/11/17 9:53
     */
    public static function orderPrint($orderId)
    {
        try {
            $order = self::getOrderInfo($orderId);
            (new YlyPrinterService())->startPrint($order, PrinterEnum::ORDER_PAY);
        } catch (\Exception $e) {
            self::handleCatch($e);
        }
    }

    public static function getOrderInfo($orderId)
    {
        $field = [
            'id',
            'sn',
            'order_type',
            'pay_way',
            'delivery_type',
            'goods_price',
            'order_amount',
            'discount_amount',
            'express_price',
            'user_remark',
            'pickup_code',
            'address',
            'selffetch_shop_id',
            'create_time',
            'pickup_code',
            'change_price',
            'member_amount',
        ];
        $order = Order::field($field)->with(['orderGoods' => function($query) {
            $query->field(['goods_num', 'order_id', 'goods_price', 'goods_snap', 'original_price' ]);
        }])
            ->append(['delivery_address', 'pay_way_desc', 'delivery_type_desc'])
            ->findOrEmpty($orderId);
        if ($order->isEmpty()) {
            throw new \Exception("订单不存在");
        }
        // 门店自提
        if ($order->delivery_type == DeliveryEnum::SELF_DELIVERY) {
            $field = [
                'id',
                'name',
                'contact',
                'province',
                'city',
                'district',
                'address',
            ];
            $selffetchShop = SelffetchShop::field($field)
                ->append(['detailed_address'])
                ->findOrEmpty($order->selffetch_shop_id);
            $order->selffetch_shop = $selffetchShop;
        }

        return $order->toArray();
    }

    /**
     * @notes 处理易联云异常
     * @param $e
     * @return false
     * @author Tab
     * @date 2021/11/17 10:01
     */
    public static function handleCatch($e)
    {
        $msg = json_decode($e->getMessage(),true);
        if(18 === $e->getCode()){
            //access_token过期，清除缓存中的access_token
            (new YlyPrinterCache())->deleteTag();
        };
        if($msg && isset($msg['error'])){
            Log::write('小票打印出错1:易联云'.$msg['error_description']);
        }
        Log::write('小票打印出错2：'.$e->getMessage());
    }
}
