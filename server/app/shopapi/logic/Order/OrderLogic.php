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


namespace app\shopapi\logic\Order;


use app\common\cache\HandleConcurrencyCache;
use app\common\enum\AfterSaleEnum;
use app\common\enum\AfterSaleLogEnum;
use app\common\enum\BargainEnum;
use app\common\enum\CouponEnum;
use app\common\enum\DeliveryEnum;
use app\common\enum\FreeShippingEnum;
use app\common\enum\GoodsEnum;
use app\common\enum\OrderEnum;
use app\common\enum\OrderLogEnum;
use app\common\enum\PayEnum;
use app\common\enum\PresellEnum;
use app\common\enum\TeamEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\CommonPresellLogic;
use app\common\logic\DiscountLogic;
use app\common\model\AddressLibrary;
use app\common\model\AfterSale;
use app\common\model\AfterSaleGoods;
use app\common\model\BargainInitiate;
use app\common\model\Cart;
use app\common\model\CouponList;
use app\common\model\Delivery;
use app\common\model\Express;
use app\common\model\FreeShipping;
use app\common\model\FreeShippingOrder;
use app\common\model\Goods;
use app\common\model\GoodsItem;
use app\common\model\LuckyDrawRecord;
use app\common\model\Order;
use app\common\model\OrderGoods;
use app\common\model\OrderLog;
use app\common\model\PresellGoodsItem;
use app\common\model\Region;
use app\common\model\SeckillGoodsItem;
use app\common\model\SelffetchShop;
use app\common\model\TeamActivity;
use app\common\model\TeamFound;
use app\common\model\TeamGoods;
use app\common\model\TeamGoodsItem;
use app\common\model\TeamJoin;
use app\common\model\User;
use app\shopapi\logic\activity\DeductService;
use app\common\logic\BaseLogic;
use app\common\model\UserAddress;
use app\common\service\after_sale\AfterSaleService;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\shopapi\logic\BargainLogic;
use app\shopapi\logic\TeamLogic;
use expressage\Kd100;
use expressage\Kdniao;
use think\Exception;
use think\facade\Db;
use think\facade\Validate;

/**
 * 订单逻辑
 * Class OrderLogic
 * @package app\shopapi\logic
 */
class OrderLogic extends BaseLogic
{

    /**
     * 下单用户
     * @var
     */
    protected static $user;

    /**
     * 订单类型
     * @var int
     */
    protected static $OrderType = OrderEnum::NORMAL_ORDER;


    /**
     * 订单数量
     * @var int
     */
    protected static $totalNum = 0;


    /**
     * 订单金额
     * @var array
     */
    protected static $orderPrice = [
        'total_goods_price'     => 0,//订单商品金额
        'express_price'         => 0,//运费
        'total_amount'          => 0,//订单金额
        'order_amount'          => 0,//订单实付金额
        'discount_amount'       => 0,//优惠金额
        'member_amount'         => 0,//会员折扣价
        'total_goods_original_price'  => 0,//订单商品原价总价
    ];
    
    /**
     * 失效商品列表
     * @var array
     */
    protected static array $disabledGoods = [];



    /**
     * @notes 当前下单用户
     * @param $userId
     * @return array
     * @author 段誉
     * @date 2021/7/23 15:52
     */
    public static function setOrderUser($userId)
    {
        self::$user = User::findOrEmpty($userId)->toArray();
        return self::$user;
    }


    /**
     * @notes 订单结算详情
     * @param $params
     * @return array|bool
     * @author 段誉
     * @date 2021/7/23 15:52
     */
    public static function settlement($params)
    {
        try {
            //设置订单类型
            self::$OrderType = $params['order_type'];

            //设置用户信息
            $user = self::setOrderUser($params['user_id']);

            //设置用户地址
            $userAddress = UserAddress::getOneAddress($params['user_id'], $params['address_id'] ?? 0);

            //获取商品信息
            $goodsLists = self::getOrderGoodsData($params);

            //判断是否需要地址
            $is_address = 1;
            foreach ($goodsLists as $goods) {
                //非虚拟商品必填地址
                if ($goods['type'] != GoodsEnum::GOODS_VIRTUAL) {
                    break;
                }
                $is_address = $goods['is_address'];
            }
            if ($is_address == 0) {
                $userAddress = [];
            }

            //计算运费(自提订单不需要运费)
            if ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY) {
                self::$orderPrice['express_price'] = 0;
            } else {
                $express_data = FreightLogic::calculateFreight($goodsLists, $userAddress);
                self::$orderPrice['express_price'] = $express_data['express_price'];
                $goodsLists = $express_data['goods'];
            }

            // 普通订单、虚拟订单可使用优惠券
            if (!empty($params['coupon_list_id']) && in_array(self::$OrderType, [OrderEnum::NORMAL_ORDER, OrderEnum::VIRTUAL_ORDER])) {
                $discountData = OrderCouponLogic::calculateCouponDiscount($goodsLists, $params['coupon_list_id']);
                self::$orderPrice['discount_amount'] = $discountData['discount'];
                self::$orderPrice['order_amount'] -= $discountData['discount'];
                $goodsLists = $discountData['goods'];
            }

            // 订单金额
            self::$orderPrice['total_amount'] += self::$orderPrice['express_price'];

            //订单应付金额
            self::$orderPrice['order_amount'] += self::$orderPrice['express_price'];

            $result = [
                'terminal'              => $params['terminal'],
                'delivery_type'         => intval($params['delivery_type']),
                'delivery_type_desc'    => DeliveryEnum::getDeliveryTypeDesc($params['delivery_type']),
                'cart_id'               => $params['cart_id'] ?? [],
                'order_type'            => self::$OrderType,
                'coupon_list_id'        => intval($params['coupon_list_id'] ?? 0),
                'total_num'             => self::$totalNum,

                'total_goods_price'             => bcadd(0, self::$orderPrice['total_goods_price'], 2),
                'total_amount'                  => bcadd(0, self::$orderPrice['total_amount'], 2),
                'order_amount'                  => bcadd(0, self::$orderPrice['order_amount'], 2),
                'discount_amount'               => bcadd(0, self::$orderPrice['discount_amount'], 2),
                'member_amount'                 => bcadd(0, self::$orderPrice['member_amount'],2),
                'express_price'                 => bcadd(0, self::$orderPrice['express_price'], 2),
                'total_goods_original_price'    => bcadd(0, self::$orderPrice['total_goods_original_price'], 2),

                'user_id'               => $user['id'],
                'user_money'            => $user['user_money'],
                'six_discount_available'          => ConfigService::get('recharge', 'six_percent_discount', 0) && intval($user['six_discount'] ?? 0) === 1 && floatval($user['user_money'] ?? 0) > 0,
                'six_discount_amount'             => bcadd(0, bcmul(self::$orderPrice['order_amount'], '0.6', 2), 2),
                'six_discount_save'               => bcadd(0, bcmul(self::$orderPrice['order_amount'], '0.4', 2), 2),
                'user_remark'           => $params['user_remark'] ?? '',
                'address'               => $userAddress,

                'goods'                 => $goodsLists,
                'goods_disabled'        => static::$disabledGoods,
                'selffetch_shop_id'     => $params['selffetch_shop_id'] ?? '',
                'selffetch_info'        => [],
                'contact'               => $params['contact'] ?? '',
                'mobile'                => $params['mobile'] ?? '',

                'is_address'            => $is_address,
                'draw_record_id'        => $params['draw_record_id'] ?? 0,
            ];
            //门店自提显示上次提货人信息
            if ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY) {
                $selffetch_info = Order::where(['user_id' => $user['id'], 'delivery_type' => DeliveryEnum::SELF_DELIVERY])
                    ->field('address,selffetch_shop_id')
                    ->order('id desc')
                    ->findOrEmpty()->toArray();

                //上一次提货人
                if ($selffetch_info) {
                    $selffetch_field = [
                        'id', 'name', 'image', 'contact', 'mobile',
                        'province', 'city', 'district', 'longitude', 'address', 'latitude',
                        'business_start_time', 'business_end_time', 'weekdays', 'remark',
                        'status',
                    ];
                    $result['selffetch_info'] = [
                        'selffetch_shop_id' => $selffetch_info['selffetch_shop_id'] ?? '',
                        'contact'           => $selffetch_info['address']->contact ?? '',
                        'mobile'            => $selffetch_info['address']->mobile ?? '',
                        'selffetch_shop'    => SelffetchShop::where('status', 1)
                            ->field($selffetch_field)
                            ->append([ 'detailed_address' ])
                            ->find($selffetch_info['selffetch_shop_id'] ?? 0),
                    ];
                }
            }
            // 营销活动附加参数: 秒杀、拼团、砍价
            switch ($params['order_type']) {
                // 拼团
                case OrderEnum::TEAM_ORDER:
                    $result['team_id'] = $params['team_id'];
                    $result['found_id'] = $params['found_id'] ?? null;
                    $teamFound = (new TeamFound())
                        ->where(['id' => $result['found_id'], 'user_id' => $params['user_id']])
                        ->findOrEmpty();
                    if (!$teamFound->isEmpty()) {
                        self::$error = '不允许参与自己开的团';
                        return false;
                    }
                    break;
                // 秒杀
                case OrderEnum::SECKILL_ORDER:
                    $result['seckill_id'] = $params['seckill_id'];
                    break;
                // 砍价
                case OrderEnum::BARGAIN_ORDER:
                    $result['initiate_id'] = $params['initiate_id'];
                    break;
                // 预售
                case OrderEnum::PRESELL_ORDER:
                    $result['presell_id'] = $params['presell_id'];
                    break;
                // 抽奖
                case OrderEnum::DRAW_ORDER:
                    //订单金额等于运费
                    $result['order_amount'] = $result['express_price'];
                    $result['total_amount'] = $result['express_price'];
                    break;
            }
    
            // 判断是否符合包邮活动条件
            if (self::isFreeShipping($result)) {
                $result['order_amount'] = $result['order_amount'] - $result['express_price'];
                $result['total_amount'] = $result['total_amount'] - $result['express_price'];
                $result['express_price'] = 0;
                // 商品的运费也清0
                foreach ($goodsLists as &$goodsList) {
                    $goodsList['express_price'] = 0;
                    $goodsList['express_money'] = 0;
                }
                $result['goods'] = $goodsLists;
            }
    
            $result['total_goods_price']            = bcadd($result['total_goods_price'], 0, 2);
            $result['total_amount']                 = bcadd($result['total_amount'], 0, 2);
            $result['order_amount']                 = bcadd($result['order_amount'], 0, 2);
            $result['discount_amount']              = bcadd($result['discount_amount'], 0, 2);
            $result['member_amount']                = bcadd($result['member_amount'], 0, 2);
            $result['express_price']                = bcadd($result['express_price'], 0, 2);
            $result['total_goods_original_price']   = bcadd($result['total_goods_original_price'], 0, 2);
            
            // 充值抵扣计算
            $deductInfo = DeductService::check();
            if ($deductInfo['active']) {
                $user = User::find($params['user_id']);
                $calc = DeductService::calculate(
                    $result['order_amount'],
                    $user['activity_money'] ?? 0,
                    $deductInfo['ratio']
                );
                $result['deduct_amount'] = $calc['deduct_amount'];
                $result['pay_amount'] = $calc['pay_amount'];
                $result['activity_ratio'] = $deductInfo['ratio'];
            } else {
                $result['deduct_amount'] = 0;
                $result['pay_amount'] = $result['order_amount'];
            }

            return $result;

        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 是否符合包邮活动条件
     */
    public static function isFreeShipping(&$params) {
        $params['is_free_shipping'] = false;
        if (empty($params['address'])) {
            return false;
        }
        // 获取当前时间进行中的包邮活动
        $activity = FreeShipping::where([
            ['start_time', '<=', time()],
            ['end_time', '>', time()],
            ['status', '=', FreeShippingEnum::ING],
        ])->find();
        if (!$activity) {
            // 没有包邮活动
            return false;
        }
        if ($activity->getData('condition_type') == FreeShippingEnum::BY_AMOUNT) {
            // 去除优惠金额
            $threshold = $params['total_goods_price'] - $params['discount_amount'];
        } else {
            $threshold = $params['total_num'];
        }
        $nationThreshold = 0;
        foreach($activity->region as $item) {
            if ($item->region_id == "100000") {
                // 全国区域留在最后判断
                $nationThreshold = $item->threshold;
                continue;
            }

            if (strpos($item->region_id, $params['address']->district_id) !== false) {
                if ($threshold >= $item->threshold) {
                    $params['is_free_shipping'] = true;
                    $params['free_shipping_id'] = $activity->id;
                    return true;
                } else {
                    return false;
                }
            }

            if (strpos($item->region_id, $params['address']->city_id) !== false) {
                if ($threshold >= $item->threshold) {
                    $params['is_free_shipping'] = true;
                    $params['free_shipping_id'] = $activity->id;
                    return true;
                } else {
                    return false;
                }
            }

            if (strpos($item->region_id, $params['address']->province_id) !== false) {
                if ($threshold >= $item->threshold) {
                    $params['is_free_shipping'] = true;
                    $params['free_shipping_id'] = $activity->id;
                    return true;
                } else {
                    return false;
                }
            }
        }
        if ($threshold >= $nationThreshold) {
            $params['is_free_shipping'] = true;
            $params['free_shipping_id'] = $activity->id;
            return true;
        }
        return false;
    }


    /**
     * @notes 提交订单前验证
     * @param $params
     * @throws \Exception
     * @author 段誉
     * @date 2021/7/23 15:53
     */
    public static function submitBeforeCheck($params)
    {
        // 商品检测
        if (empty($params['goods'])) {
            throw new \Exception('提交的商品已不能购买，请重新选择商品');
        }
        //配送方式为快递配送时,检测地址
        if (empty($params['address']) && $params['delivery_type'] == DeliveryEnum::EXPRESS_DELIVERY && $params['is_address'] == 1) {
            throw new \Exception('请选择收货地址');
        }

        //配送方式为门店自提时
        if ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY && empty($params['selffetch_shop_id'])) {
            throw  new \Exception('自提门店不能为空');
        }
        if ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY && !empty($params['selffetch_shop_id'])) {
            $selffetch_shop = SelffetchShop::where('id', $params['selffetch_shop_id'])->findOrEmpty();
            if ($selffetch_shop->isEmpty()) {
                throw  new \Exception('自提门店不存在');
            }
        }
        if ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY && empty($params['contact'])) {
            throw  new \Exception('取货人不能为空');
        }
        if ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY && empty($params['mobile'])) {
            throw  new \Exception('联系电话不能为空');
        }

        if ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY && !Validate::mobile($params['mobile'])) {
            throw  new \Exception('联系电话格式不正确');
        }

        // 校验商品数量
        $limit_arr = [];
        foreach ($params['goods'] as $goods) {
            if ($goods['goods_num'] <= 0) {
                throw  new \Exception('请选择商品' . ($goods['goods_name'] ?? '') . '数量');
            }

            $limit_arr[$goods['goods_id']]['goods_num'] = ($limit_arr[$goods['goods_id']]['goods_num'] ?? 0) + $goods['goods_num'];
            $limit_arr[$goods['goods_id']]['limit_type'] = $goods['limit_type'];
            $limit_arr[$goods['goods_id']]['limit_value'] = $goods['limit_value'];
            $limit_arr[$goods['goods_id']]['goods_name'] = $goods['goods_name'];
            $limit_arr[$goods['goods_id']]['goods_id'] = $goods['goods_id'];
        }

        //校验限购商品
        foreach ($limit_arr as $limit_val) {
            if (static::isOutBuyNum($limit_val, $limit_val['goods_num'], $params['user_id'])) {
                throw  new \Exception('商品：' . ($limit_val['goods_name'] ?? '') . ' 超过限购数量');
            }
        }

        //验证订单商品是否支持对应的配送方式
        $is_express = ConfigService::get('delivery_type', 'is_express', 1);
        $is_selffetch = ConfigService::get('delivery_type', 'is_selffetch', 0);
        $item_ids = implode(',', array_column($params['goods'], 'item_id'));
        $goods_ids = implode(',', GoodsItem::where('id', 'in', $item_ids)->column('goods_id'));
        $goods = Goods::where('id', 'in', $goods_ids)->select();
        $goods_name = [];

        //门店自提
        if ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY) {
            if ($is_selffetch == 0) {
                throw  new \Exception('系统未开启门店自提配送方式');
            }

            foreach ($goods as $val) {
                //虚拟商品不支持门店自提
                if (GoodsEnum::GOODS_VIRTUAL == $val['type']) {
                    throw new \Exception($val['name'] . '不支持门店自提');
                }
                if ($val['is_selffetch'] == 0) {
                    $goods_name[] = $val['name'];
                }
            }
        } elseif ($params['delivery_type'] == DeliveryEnum::EXPRESS_DELIVERY) { //快递配送
            if ($is_express == 0) {
                throw  new \Exception('系统未开启快递配送方式');
            }

            foreach ($goods as $val) {
                //实物商品且不支持物流
                if ($val['is_express'] == 0 && GoodsEnum::GOODS_REALITY == $val['type']) {
                    $goods_name[] = $val['name'];
                }
            }
        }
        if (!empty($goods_name)) {
            throw new \Exception(implode('、', $goods_name) . '不支持' . DeliveryEnum::getDeliveryTypeDesc($params['delivery_type']) . ',请重新选择配送方式');
        }
    }


    /**
     * @notes 提交订单
     * @param $params
     * @return array|bool
     * @author 段誉
     * @date 2021/7/23 15:53
     */
    public static function submitOrder($params)
    {
        Db::startTrans();
        try {
            //提交前验证
            self::submitBeforeCheck($params);

            //防重复下单 同一用户10秒内只能提交一次
            $HandleConcurrencyCache = new HandleConcurrencyCache();
            $submitOrderLimit = $HandleConcurrencyCache->getCache($HandleConcurrencyCache->getSubmitOrderLimitKey(self::$user['id']));
            if ($submitOrderLimit && time() - $submitOrderLimit < 10) {
                throw new \Exception('您有订单正在处理中，请稍后再试');
            } else {
                $HandleConcurrencyCache->setCache($HandleConcurrencyCache->getSubmitOrderLimitKey(self::$user['id']), time(), 10);
            }

            //删除购物车
            self::delCartByOrder($params);

            //下单扣除库存
            self::decStock($params['goods']);

            //提交订单
            $order = self::addOrder($params, self::$user['id']);

            //下单增加商品销量
            self::incSale($params['goods']);

            //有使用优惠券时更新coupon_list
            if ($params['coupon_list_id'] > 0) {
                self::handleCouponByOrder($params['coupon_list_id'], $order['id']);
            }

            // 营销活动下单后的操作
            switch ($params['order_type']) {
                // 拼团
                case OrderEnum::TEAM_ORDER:
//                    self::teamAfter($params, $order);
                    break;
                // 秒杀
                case OrderEnum::SECKILL_ORDER:
                    self::seckillAfter($params, $order);
                    break;
                // 砍价
                case OrderEnum::BARGAIN_ORDER:
                    self::bargainAfter($params, $order);
                    break;
                // 预售
                case OrderEnum::PRESELL_ORDER:
                    self::presellAfter($params, $order);
                    break;
            }

            // 判断是否为包邮活动订单
            if ($params['is_free_shipping']) {
                FreeShippingOrder::create([
                    'free_shpping_id' => $params['free_shipping_id'],
                    'order_id' => $order['id'],
                    'amount' => $order['order_amount']
                ]);
            }

            //订单日志
            (new OrderLog())->record([
                'type' => OrderLogEnum::TYPE_USER,
                'channel' => OrderLogEnum::USER_ADD_ORDER,
                'order_id' => $order['id'],
                'operator_id' => self::$user['id'],
            ]);

            //抽奖记录结算
            LuckyDrawRecord::update([
                'id' => $params['draw_record_id'] ?? 0,
                'is_send' => YesNoEnum::YES,
                'send_time' => time(),
            ]);

            //提交事务
            Db::commit();
            return ['order_id' => $order['id'], 'type' => 'order'];
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 更新订单优惠券状态
     * @param $coupon_list_id
     * @param $order_id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2021/9/14 11:39 上午
     */
    public static function handleCouponByOrder($coupon_list_id, $order_id)
    {
        $coupon_list = CouponList::find($coupon_list_id);
        $coupon_list->order_id = $order_id;
        $coupon_list->status = CouponEnum::USE_STATUS_OK;
        $coupon_list->use_time = time();
        $coupon_list->update_time = time();
        $coupon_list->save();
    }


    /**
     * @notes 扣减库存（使用悲观锁防止高并发超卖）
     * @param $goodLists
     * @throws \Exception
     * @author 段誉
     * @date 2021/8/9 17:47
     */
    public static function decStock($goodLists)
    {
        // 按商品ID排序，确保所有事务都按相同顺序更新，防止死锁
        $itemIds = array_column($goodLists, 'item_id');
        array_multisort($itemIds, SORT_ASC, $goodLists);

        foreach ($goodLists as $goods) {
            // 使用悲观锁锁定记录
            $goodsItem = GoodsItem::where('id', $goods['item_id'])
                ->lock(true)  // 添加 FOR UPDATE 锁
                ->find();

            if (!$goodsItem) {
                throw new \Exception('商品不存在');
            }

            if ($goodsItem['stock'] < $goods['goods_num']) {
                throw new \Exception('商品库存不足');
            }

            // 更新库存
            $goodsItem->stock = $goodsItem->stock - $goods['goods_num'];
            $goodsItem->save();

            // 更新商品总库存 - 使用子查询直接更新，避免查询和更新之间的间隙
            $table = GoodsItem::getTable();
            Goods::where(['id' => $goods['goods_id']])
                ->update([
                    'total_stock' => Db::raw("(SELECT SUM(stock) FROM {$table} WHERE goods_id = " . $goods['goods_id'] . ")")
                ]);
        }
    }

    /**
     * @notes 下单增加销量
     * @param $goodLists
     * @throws \Exception
     * @author ljj
     * @date 2021/9/22 5:30 下午
     */
    public static function incSale($goodLists)
    {
        foreach ($goodLists as $goods) {
            Goods::update(['sales_num' => ['inc', $goods['goods_num']]], ['id' => $goods['goods_id']]);
        }
    }


    /**
     * @notes 删除购物车(购物车下单情况)
     * @param $params
     * @author 段誉
     * @date 2021/7/23 15:54
     */
    public static function delCartByOrder($params)
    {
        if (!empty($params['cart_id'])) {
            Cart::where(['id' => $params['cart_id'], 'user_id' => self::$user['id']])->delete();
        }
    }


    /**
     * @notes 添加订单
     * @param $params
     * @param $user_id
     * @author 段誉
     * @date 2021/8/9 17:42
     */
    public static function addOrder($params, $user_id)
    {
        $order = Order::create([
            'sn'                => generate_sn((new Order()), 'sn'),
            'order_type'        => $params['order_type'],
            'user_id'           => $user_id,
            'order_terminal'    => $params['terminal'],
            'coupon_list_id'    => $params['coupon_list_id'],
            'total_num'         => $params['total_num'],
            'total_amount'      => $params['total_amount'],
            'goods_price'       => $params['total_goods_price'],
            'order_amount'      => $params['order_amount'],
            'express_price'     => $params['express_price'],
            'discount_amount'   => $params['discount_amount'] + ($params['six_discount_amount'] ?? 0),
            'member_amount'     => $params['member_amount'],
            'deduct_amount'     => $params['deduct_amount'] ?? 0.00,
            'user_remark'       => $params['user_remark'],
            'address'       => [
                'contact'       => ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? $params['contact'] : ($params['address']['contact'] ?? ''),
                'province'      => ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? '' : ($params['address']['province_id'] ?? ''),
                'city'          => ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? '' : ($params['address']['city_id'] ?? ''),
                'district'      => ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? '' : ($params['address']['district_id'] ?? ''),
                'address'       => ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? '' : ($params['address']['address'] ?? ''),
                'mobile'        => ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? $params['mobile'] : ($params['address']['mobile'] ?? ''),
            ],
            'delivery_type'     => OrderEnum::VIRTUAL_ORDER == $params['order_type'] ? DeliveryEnum::DELIVERY_VIRTUAL : $params['delivery_type'],
            'pickup_code'       => ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? create_number_sn((new Order()), 'pickup_code', 6) : null,
            'selffetch_shop_id' => ($params['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? $params['selffetch_shop_id'] : 0,
            'belong_store_id'   => intval(self::$user['bind_store_id'] ?? 0),
            'draw_record_id'    => $params['draw_record_id'] ?? 0,
            'team_activity_id'     => $params['team_id'] ?? 0,
            'team_found_id'     => $params['found_id'] ?? 0,
        ]);

        $goodsData = [];
        foreach ($params['goods'] as $goods) {
            //商品实付价格
            $totalPayPrice = $goods['sub_price'] - ($goods['discount_price'] ?? 0) + ($goods['express_price'] ?? 0);
            $totalPayPrice = $totalPayPrice <= 0 ? 0 : $totalPayPrice;

            $goodsData[] = [
                'order_id'          => $order['id'],
                'goods_id'          => $goods['goods_id'],
                'item_id'           => $goods['item_id'],
                'goods_name'        => $goods['goods_name'],
                'goods_num'         => $goods['goods_num'],
                'goods_price'       => $goods['sell_price'],//商品价格单价(未扣减优惠和积分价格)
                'total_price'       => $goods['sub_price'],
                'total_pay_price'   => $totalPayPrice,//实际支付商品金额(扣除优惠金额、加上运费)
                'spec_value_ids'    => $goods['spec_value_ids'],
                'discount_price'    => $goods['discount_price'] ?? 0,//优惠券优惠金额
                'original_price'    => $goods['original_price'] ?? 0,//商品原始价格
                'member_price'      => $goods['member_price'] ?? 0,//商品会员价格
                'goods_snap'        => $goods,
                'express_price'     => $goods['express_price'] ?? 0,//运费
            ];
        }
        (new OrderGoods())->saveAll($goodsData);

        return $order;
    }


    /**
     * @notes 商品信息
     * @param $params
     * @return array
     * @author 段誉
     * @date 2021/7/23 15:54
     */
    public static function getOrderGoodsData($params)
    {
        // 购物车下单
        if ($params['source'] == 'cart') {
            $params['goods'] = Cart::where([
                ['id', 'in', $params['cart_id']],
                ['user_id', '=', $params['user_id']],
            ])->field('item_id,goods_id,goods_num')->select()->toArray();
        }

        // 砍价商品信息提取
        if ($params['order_type'] == OrderEnum::BARGAIN_ORDER) {
            $params['goods'] = self::initiateGoods($params);
        }

        $itemIds = array_column($params['goods'], 'item_id');

        $field = [
            'gi.id', 'gi.id' => 'item_id', 'gi.image' => 'item_image',
            'gi.spec_value_str', 'spec_value_ids', 'gi.sell_price', 'gi.supply_price',
            'gi.volume', 'gi.stock', 'gi.weight', 'gi.bar_code', 'g.id' => 'goods_id',
            'g.name' => 'goods_name', 'g.type', 'g.status', 'g.delete_time', 'g.image',
            'g.express_type', 'g.express_money', 'g.express_template_id',
            'g.is_express', 'g.is_selffetch', 'g.is_express', 'g.is_virtualdelivery',
            'g.after_pay', 'g.after_delivery', 'g.delivery_content', 'g.delivery_type','g.delivery_template_id',
            'g.code', 'g.is_address', 'g.limit_type', 'g.limit_value'
        ];

        $goodsData = (new Goods())->alias('g')
            ->join('goods_item gi', 'gi.goods_id = g.id')
            ->with([ 'goods_category_index2', 'delivery_template' ])
            ->where('gi.id', 'in', $itemIds)
            ->field($field)
            ->select()->toArray();
        
        $goodsData = array_column($goodsData, null, 'id');

        //处理图片路径
        foreach ($goodsData as &$val) {
            $val['item_image'] = trim($val['item_image']) ? FileService::getFileUrl($val['item_image']) : '';
        }
    
        return self::getOrderGoodsLists($params, $goodsData);
    }


    /**
     * @notes 结算商品列表
     * @param $goods
     * @param $goodsData
     * @return array
     * @author 段誉
     * @date 2021/7/23 15:55
     */
    public static function getOrderGoodsLists($params, $goodsData)
    {
        $goods = $params['goods'];
        $goodsIds = array_column($goodsData, 'goods_id');
        $levelGoodsItem = DiscountLogic::getGoodsDiscount(self::$user['id'], $goodsIds);
        $goodsLists = [];
        foreach ($goods as $k => $item) {
            //删除没找到商品信息的商品
            if (!isset($goodsData[$item['item_id']])) {
                unset($goods[$k]);
                static::$disabledGoods[] = [
                    'msg'       => '找不到商品',
                    'goods'     => $goodsData[$item['item_id']] ?? [],
                ];
                continue;
            }

            //组装商品数据
            $goodsInfo = $goodsData[$item['item_id']];
            
            $goodsInfo['goods_num'] = intval($item['goods_num'] ?? 0);
            //记录原价
            $goodsInfo['original_price'] = $goodsInfo['sell_price'];
    
            $goodsInfo['sub_price'] = round($goodsInfo['sell_price'] * $item['goods_num'], 2);

            //当前商品是否被删除或下架
            if ($goodsInfo['delete_time'] > 0 || $goodsInfo['status'] != 1) {
                unset($goods[$k]);
                static::$disabledGoods[] = [
                    'msg'       => '商品不能购买',
                    'goods'     => $goodsInfo,
                ];
                continue;
            }

            //当前库存是否足够
            if ($item['goods_num'] > $goodsInfo['stock']) {
                unset($goods[$k]);
                static::$disabledGoods[] = [
                    'msg'       => '商品库存不足',
                    'goods'     => $goodsInfo,
                ];
                continue;
            }
            // 是否限购
            if (static::isOutBuyNum($goodsInfo, $item['goods_num'], $params['user_id'])) {
                unset($goods[$k]);
                static::$disabledGoods[] = [
                    'msg'       => '超过购买限制',
                    'goods'     => $goodsInfo,
                ];
                continue;
            }
            
            $goodsInfo['member_price'] = 0;
            if(in_array(self::$OrderType,[OrderEnum::NORMAL_ORDER,OrderEnum::VIRTUAL_ORDER])){
                //会员折扣
                $goodsInfo['member_price'] = $levelGoodsItem[$goodsInfo['goods_id']][$goodsInfo['item_id']]['discount_price'] ?? '';
            }
            // 获取不同订单类型的规格单价
            $goodsInfo['sell_price'] = self::getSellPrice($params, $goodsInfo);
    
            $goodsInfo['sub_price'] = round($goodsInfo['sell_price'] * $item['goods_num'], 2);
            
            $goodsInfo['total_original_price'] = round($goodsInfo['original_price'] * $goodsInfo['goods_num'], 2);//商品原价合计
            $goodsLists[] = $goodsInfo;

            self::$totalNum += $item['goods_num'];
            self::$orderPrice['total_goods_price'] += $goodsInfo['sub_price'];
            //普通商品，计算折扣金额
            if(in_array(self::$OrderType,[OrderEnum::NORMAL_ORDER,OrderEnum::VIRTUAL_ORDER])){
                $memberAmount = round(($goodsInfo['original_price'] - $goodsInfo['sell_price']) * $item['goods_num'],2);
                self::$orderPrice['member_amount'] += $memberAmount;
            }

            //订单商品原价总价
            self::$orderPrice['total_goods_original_price'] += $goodsInfo['total_original_price'];
        }
        //订单金额
        self::$orderPrice['total_amount'] = self::$orderPrice['total_goods_price'] + self::$orderPrice['member_amount'];
        //订单应付金额
        
        self::$orderPrice['order_amount'] = self::$orderPrice['total_goods_price'];
        return $goodsLists;
    }
    
    /**
     * @notes 是否超出 购买限制
     * @param $goods
     * @param $num
     * @param $userId
     * @return bool
     * @author lbzy
     * @datetime 2023-08-16 15:13:47
     */
    static function isOutBuyNum($goods, $num, $userId) : bool
    {
        if ($goods['limit_type'] == GoodsEnum::LIMIT_TYPE_USER) {
            $order_goods_num = OrderGoods::alias('og')
                ->join('order o', 'o.id = og.order_id')
                ->where([
                        'og.goods_id'=>$goods['goods_id'],
                        'o.order_status'=>[
                            OrderEnum::STATUS_WAIT_PAY,
                            OrderEnum::STATUS_WAIT_DELIVERY,
                            OrderEnum::STATUS_WAIT_RECEIVE,
                            OrderEnum::STATUS_FINISH
                        ],
                        'o.user_id' => $userId,
                        'o.order_type' => [ OrderEnum::NORMAL_ORDER, OrderEnum::VIRTUAL_ORDER] ]
                )
                ->sum('og.goods_num');
            return ($order_goods_num + $goods['goods_num']) > $goods['limit_value'];
        }
        if ($goods['limit_type'] == GoodsEnum::LIMIT_TYPE_ORDER) {
            return $num > $goods['limit_value'];
        }
        
        return false;
    }


    /**
     * @notes 订单详情
     * @param $params
     * @return array
     * @author 段誉
     * @date 2021/8/2 20:59
     */
    public static function getDetail($params)
    {
        $result = (new Order())->with(['order_goods' => function ($query) {
            $query->field([
                'id', 'order_id', 'goods_id', 'item_id', 'goods_snap', 'original_price',
                'goods_name', 'goods_price', 'goods_num', 'total_price', 'total_pay_price', 'express_price', 'change_price', 'discount_price', 'discount_price'=>'coupon_discount', 'member_price', 'integral_price'
            ])->append(['goods_image', 'spec_value_str','original_price'])->hidden(['goods_snap']);
        }])
            ->where(['id' => $params['id'], 'user_id' => $params['user_id']])
            ->append(['btn', 'delivery_address', 'cancel_unpaid_orders_time', 'show_pickup_code'])
            ->hidden(['user_id', 'order_terminal', 'delete_time', 'update_time'])
            ->findOrEmpty()->toArray();

        //订单类型
        $result['order_type_desc'] = ($result['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? '自提订单' : OrderEnum::getOrderTypeDesc($result['order_type']);

        //订单状态描述
        $result['order_status_desc'] = ($result['order_status'] == OrderEnum::STATUS_WAIT_DELIVERY && $result['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? '待取货' : OrderEnum::getOrderStatusDesc($result['order_status']);
        if ($result['order_type'] == OrderEnum::TEAM_ORDER && $result['is_team_success'] != TeamEnum::TEAM_FOUND_SUCCESS) {
            $result['order_status_desc'] = ($result['order_status'] == OrderEnum::STATUS_WAIT_DELIVERY) ? TeamEnum::getStatusDesc($result['is_team_success']) : OrderEnum::getOrderStatusDesc($result['order_status']);
        }
        
        // 预售信息
        if ($result['order_type'] == OrderEnum::PRESELL_ORDER) {
            $result['presell'] = CommonPresellLogic::orderInfo($result);
        }

        //自提门店
        $result['selffetch_shop'] = SelffetchShop::where('id', $result['selffetch_shop_id'])
            ->append(['detailed_address'])
            ->hidden([ 'create_time', 'update_time', 'delete_time' ])
            ->find();

        //地址  省市区分隔开
        $result['address']->province = Region::where('id', $result['address']->province)->value('name');
        $result['address']->city = Region::where('id', $result['address']->city)->value('name');
        $result['address']->district = Region::where('id', $result['address']->district)->value('name');

        //订单商品原价总价
        $result['total_original_price'] = 0;

        //订单商品售后按钮处理
        foreach ($result['order_goods'] as &$goods) {
            $goods['after_sale_btn'] = 0;//售后按钮关闭
            $after_sale = AfterSale::where(['order_goods_id' => $goods['id'], 'order_id' => $params['id']])->findOrEmpty();
            $after_sale_goods = AfterSaleGoods::where(['order_goods_id' => $goods['id'], 'after_sale_id' => $after_sale['id']])->findOrEmpty();
            $goods['after_sale_id'] = $after_sale_goods['id'] ?? 0;

            if ($result['order_status'] == OrderEnum::STATUS_FINISH && $result['after_sale_deadline'] > time() && $after_sale->isEmpty()) {
                $goods['after_sale_btn'] = 1;//售后按钮开启
            }
            if ($result['order_status'] == OrderEnum::STATUS_FINISH && $result['after_sale_deadline'] > time() && $after_sale['status'] == AfterSaleEnum::STATUS_ING) {
                $goods['after_sale_btn'] = 2;//售后中
            }
            if ($result['order_status'] == OrderEnum::STATUS_FINISH && $result['after_sale_deadline'] > time() && $after_sale['status'] == AfterSaleEnum::STATUS_SUCCESS) {
                $goods['after_sale_btn'] = 3;//售后成功
            }
            if ($result['order_status'] == OrderEnum::STATUS_FINISH && $result['after_sale_deadline'] > time() && $after_sale['status'] == AfterSaleEnum::STATUS_FAIL) {
                $goods['after_sale_btn'] = 4;//售后失败
            }


            //批量下单后，过了取消订单的时间，可对其中一件商品进行退款操作
            if (in_array($result['order_status'],[OrderEnum::STATUS_WAIT_DELIVERY,OrderEnum::STATUS_WAIT_RECEIVE])) {
                $ableCancelOrder = ConfigService::get('transaction', 'cancel_unshipped_orders');
                if ($ableCancelOrder == YesNoEnum::NO) {
                    $goods['after_sale_btn'] = 1;//售后按钮开启
                }
                if ($ableCancelOrder == YesNoEnum::YES) {
                    $configTime = ConfigService::get('transaction', 'cancel_unshipped_orders_times');
                    $ableCancelTime = strtotime($result['pay_time']) + ($configTime * 60);
                    if (time() > $ableCancelTime) {
                        $goods['after_sale_btn'] = 1;//售后按钮开启
                    }
                }
                if (isset($after_sale['status']) && $after_sale['status'] == AfterSaleEnum::STATUS_ING) {
                    $goods['after_sale_btn'] = 2;//售后中
                }
                if (isset($after_sale['status']) && $after_sale['status'] == AfterSaleEnum::STATUS_SUCCESS) {
                    $goods['after_sale_btn'] = 3;//售后成功
                }
                if (isset($after_sale['status']) && $after_sale['status'] == AfterSaleEnum::STATUS_FAIL) {
                    $goods['after_sale_btn'] = 4;//售后失败
                }
            }

            //商品原价总价
            $goods['total_original_price'] = $goods['original_price'] * $goods['goods_num'];
            $result['total_original_price'] += $goods['original_price'] * $goods['goods_num'];

            //会员价优惠
            $goods['member_discount'] = 0;
            if($goods['member_price'] > 0){
                $goods['member_discount'] = round(($goods['original_price'] - $goods['member_price']) * $goods['goods_num'],2);
            }
            // unset($goods['member_price']);
            //积分优惠
            $goods['integral_discount'] = round($goods['integral_price'] * $goods['goods_num'],2);
            // unset($goods['integral_price']);

            //售后状态
            $goods['after_sale_status_desc'] = '无售后';
            if (!$after_sale->isEmpty()) {
                $goods['after_sale_status_desc'] = AfterSaleEnum::getStatusDesc($after_sale->status);
            }

            //订单商品总的优惠
            $goods['total_discount'] = round($goods['member_discount']+ $goods['coupon_discount'] +$goods['integral_discount'],2);
        }
        //订单商品原价总价
        $result['total_original_price'] = round($result['total_original_price'],2);
        //订单总优惠金额
        $result['total_discount'] = $result['discount_amount'] + $result['member_amount'] + $result['integral_amount'];
        // unset($result['discount_amount']);unset($result['member_amount']);unset($result['integral_amount']);
        //商品详情屏蔽查看内容按钮
        $result['btn']['content_btn'] = OrderEnum::BTN_HIDE;
        return $result;
    }
    
    static function wxReceiveDetail($id, $user_id)
    {
        $order = Order::where('id', $id)->where('user_id', $user_id)->findOrEmpty()->toArray();
        
        return [
            'transaction_id'    => $order['transaction_id'] ?? '',
        ];
    }


    /**
     * @notes 取消订单
     * @param $params
     * @return bool
     * @author 段誉
     * @date 2021/8/2 15:08
     */
    public static function cancelOrder($params)
    {
        Db::startTrans();
        try {
            $order = (new Order())->getUserOrderById($params['id'], $params['user_id']);

            // 如果是拼团订单特别处理
            if ($order['order_type'] == OrderEnum::TEAM_ORDER) {

                TeamLogic::signFailTeam($order['id']);

            } else {

                //处于已支付状态的发起整单售后
                if ($order['pay_status'] == PayEnum::ISPAID) {
                    AfterSaleService::orderRefund([
                        'order_id' => $params['id'],
                        'scene' => AfterSaleLogEnum::BUYER_CANCEL_ORDER
                    ]);
                }

                //更新订单为已关闭
                Order::update([
                    'order_status' => OrderEnum::STATUS_CLOSE,
                    'cancel_time' => time()
                ], ['id' => $order['id']]);

                $returnInventory = ConfigService::get('transaction', 'return_inventory');
                if ($returnInventory) {
                    // 需退还库存
                    AfterSaleService::returnInventory(['order_id' => $order['id']]);
                }

                $returnCoupon = ConfigService::get('transaction', 'return_coupon');
                if ($returnCoupon) {
                    // 需退还优惠券
                    AfterSaleService::returnCoupon($order);
                }

                //订单日志
                (new OrderLog())->record([
                    'type' => OrderLogEnum::TYPE_USER,
                    'channel' => OrderLogEnum::USER_CANCEL_ORDER,
                    'order_id' => $params['id'],
                    'operator_id' => $params['user_id'],
                ]);

            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }


    /**
     * @notes 确认订单
     * @param $params
     * @author 段誉
     * @date 2021/8/2 14:59
     */
    public static function confirmOrder($params)
    {
        //更新订单状态
        Order::update([
            'order_status' => OrderEnum::STATUS_FINISH,
            'confirm_take_time' => time(),
            'after_sale_deadline' => self::getAfterSaleDeadline(), //售后截止时间
        ], ['id' => $params['id'], 'user_id' => $params['user_id']]);

        //订单日志
        (new OrderLog())->record([
            'type' => OrderLogEnum::TYPE_USER,
            'channel' => OrderLogEnum::USER_CONFIRM_ORDER,
            'order_id' => $params['id'],
            'operator_id' => $params['user_id']
        ]);
    }


    /**
     * @notes 获取当前售后
     * @return float|int
     * @author 段誉
     * @date 2021/8/2 17:02
     */
    public static function getAfterSaleDeadline()
    {
        //是否关闭维权
        $afterSale = ConfigService::get('transaction', 'after_sales');
        //可维权时间
        $afterSaleDays = ConfigService::get('transaction', 'after_sales_days');

        if ($afterSale == YesNoEnum::NO) {
            $afterSaleDeadline = time();
        } else {
            $afterSaleDeadline = ($afterSaleDays * 24 * 60 * 60) + time();
        }

        return $afterSaleDeadline;
    }


    /**
     * @notes 查看物流
     * @param $params
     * @return array[]
     * @author ljj
     * @date 2021/8/13 6:07 下午
     */
    public static function orderTraces($params)
    {
        //订单信息
        $order = Order::field('id,sn,pay_time,address,order_type,express_status,order_status')
            ->append(['delivery_address'])
            ->where('id',$params['id'])
            ->find()
            ->toArray();

        //物流配置
        $express_type = ConfigService::get('logistics_config', 'express_type', '');
        $express_bird = unserialize(ConfigService::get('logistics_config', 'express_bird', ''));
        $express_hundred = unserialize(ConfigService::get('logistics_config', 'express_hundred', ''));

        $parcelInfo = Delivery::field('id,order_id,order_goods_info,express_name,express_id,invoice_no,send_type,remark,create_time,delivery_address_info,return_address_info')
            ->where(['order_id'=>$order['id']])
            ->append(['send_type_desc'])
            ->select()->toArray();
        foreach ($parcelInfo as $key=>$parcel) {
            //查询物流信息
            $logisticsInfo = [];
            if ($parcel['send_type'] == DeliveryEnum::NO_EXPRESS) {
                $logisticsInfo['traces'] = ['无需物流'];
            } else {
                if (empty($express_type) || (empty($express_bird) && empty($express_hundred))) {
                    $logisticsInfo['traces'] = ['暂无物流信息'];
                } else {
                    //快递配置设置为快递鸟时
                    if($express_type === 'express_bird') {
                        $expressage = (new Kdniao($express_bird['ebussiness_id'], $express_bird['app_key']));
                        $express_field = 'codebird';
                    } elseif($express_type === 'express_hundred') {
                        $expressage = (new Kd100($express_hundred['customer'], $express_hundred['app_key']));
                        $express_field = 'code100';
                    }
                    $logisticsInfo['express_field'] = $express_field ?? 'codebird';
                    //快递编码
                    $express_code = Express::where('id',$parcel['express_id'])->value($express_field);
                    //获取物流轨迹
//                if (in_array(strtolower($express_code), [ 'sf', 'shunfeng' ])) {
//                    if ($express_type === 'express_bird') {
//                        $expressage->logistics($express_code, $parcel['invoice_no'], substr($order['address']->mobile, -4));
//                    } else {
//                        $expressage->logistics($express_code, $parcel['invoice_no'], $order['address']->mobile);
//                    }
//                }else {
//                    $expressage->logistics($express_code, $parcel['invoice_no']);
//                }
                    if ($express_type === 'express_bird') {
                        $expressage->logistics($express_code, $parcel['invoice_no'], substr($order['address']->mobile, -4));
                    } else {
                        $expressage->logistics($express_code, $parcel['invoice_no'], $order['address']->mobile);
                    }
                    $logisticsInfo['traces'] = $expressage->logisticsFormat();
                    if ($logisticsInfo['traces'] == false) {
                        $logisticsInfo['traces'] = ['暂无物流信息'];
                    } else {
                        foreach ($logisticsInfo['traces'] as &$item) {
                            $item = array_values(array_unique($item));
                        }
                        if ($logisticsInfo['express_field'] == 'codebird') {
                            //倒序排序
                            $logisticsInfo['traces'] = array_reverse($logisticsInfo['traces']);
                        }
                    }
                }
            }
            $parcelInfo[$key]['logistics_info'] = $logisticsInfo;
            $parcelInfo[$key]['express_icon'] = Express::where('id',$parcel['express_id'])->value('icon');
        }
        $deliveryAddressInfo = !empty($parcelInfo[0]['delivery_address_info']) ? json_decode($parcelInfo[0]['delivery_address_info'],true) : '';
        $returnAddressInfo = !empty($parcelInfo[0]['return_address_info']) ? json_decode($parcelInfo[0]['return_address_info'],true) : '';

        //待发货商品
        $waitDeliveryGoods = OrderGoods::where(['order_id'=>$order['id'],'express_status'=>[DeliveryEnum::NOT_SHIPPED,DeliveryEnum::PART_SHIPPED]])
            ->field('id,goods_name,goods_num,goods_price,delivery_num,goods_snap')
            ->append(['spec_value_str','goods_image'])
            ->hidden(['goods_snap'])->select()->toArray();
        foreach ($waitDeliveryGoods as $key=>$goods) {
            $afterSale = AfterSale::where(['order_goods_id'=>$goods['id'],'status'=>AfterSaleEnum::STATUS_SUCCESS])->findOrEmpty();
            if (!$afterSale->isEmpty()) {
                unset($waitDeliveryGoods[$key]);
            }
        }
        $waitDeliveryGoods = empty($waitDeliveryGoods) ? [] : array_values($waitDeliveryGoods);

        return [
            'order_id' => $order['id'],
            'order_sn' => $order['sn'],
            'order_type' => $order['order_type'],
            'order_status' => $order['order_status'],
            'express_status' => $order['express_status'],
            'pay_time' => $order['pay_time'],
            'receipt_address_info' => [
                'addresss' => $order['delivery_address'],
                'contact' => $order['address']->contact,
                'mobile' => $order['address']->mobile,
            ],
            'delivery_address_info' => [
                'addresss' => $deliveryAddressInfo['complete_address'] ?? '',
                'contact' => $deliveryAddressInfo['contact'] ?? '',
                'mobile' => $deliveryAddressInfo['mobile'] ?? '',
            ],
            'return_address_info' => [
                'addresss' => $returnAddressInfo['complete_address'] ?? '',
                'contact' => $returnAddressInfo['contact'] ?? '',
                'mobile' => $returnAddressInfo['mobile'] ?? '',
            ],
            'remark' => $parcelInfo[0]['remark'],
            'parcel_info' => $parcelInfo,
            'wait_delivery_goods' => $waitDeliveryGoods
        ];
    }

    /**
     * @notes 获取配送方式
     * @return array
     * @author ljj
     * @date 2021/8/27 2:32 下午
     */
    public function getDeliveryType()
    {
        return [
            'express' => [
                'is_express' => ConfigService::get('delivery_type', 'is_express', 1),
                'express_name' => ConfigService::get('delivery_type', 'express_name', '快递发货'),
            ],
            'selffetch' => [
                'is_selffetch' => ConfigService::get('delivery_type', 'is_selffetch', 0),
                'selffetch_name' => ConfigService::get('delivery_type', 'selffetch_name', '上门自提'),
            ],
        ];
    }

    /**
     * @notes 删除订单
     * @param $params
     * @return bool
     * @author ljj
     * @date 2021/8/31 2:38 下午
     */
    public function del($params)
    {
        return Order::destroy($params['id']);
    }

    /**
     * @notes 提取砍价商品信息
     * @param $params
     * @return array
     * @author Tab
     * @date 2021/10/9 11:13
     */
    public static function initiateGoods($params)
    {
        $bargainInitiate = BargainInitiate::findOrEmpty($params['initiate_id'])->toArray();
        // 返回二维数组
        return [
            [
                'item_id' => $bargainInitiate['goods_snapshot']['item_id'],
                'goods_num' => $bargainInitiate['goods_snapshot']['goods_num'],
            ]
        ];
    }

    /**
     * @notes 获取不同类型订单的规格单价
     * @param $params
     * @author Tab
     * @date 2021/10/9 11:23
     */
    public static function getSellPrice($params, $goodsInfo)
    {
        switch ($params['order_type']) {
            // 普通订单 虚拟订单 抽奖订单
            case OrderEnum::VIRTUAL_ORDER:
            case OrderEnum::NORMAL_ORDER:
            case OrderEnum::DRAW_ORDER:
                return self::getGoodsSellPrice($goodsInfo);
            // 拼团订单
            case OrderEnum::TEAM_ORDER:
                return self::getTeamActivityPrice($params);
            // 秒杀订单
            case OrderEnum::SECKILL_ORDER:
                return self::getSeckillActivityPrice($params);
            // 砍价订单
            case OrderEnum::BARGAIN_ORDER:
                return self::getBargainActivityPrice($params);
            // 预售订单
            case OrderEnum::PRESELL_ORDER:
                return self::getPresellActivityPrice($params);
        }
    }
    
    /**
     * @notes 获取预售价
     * @param $params
     * @return mixed
     * @author lbzy
     * @datetime 2024-04-26 16:33:58
     */
    static function getPresellActivityPrice($params)
    {
        return PresellGoodsItem::alias('pgi')
            ->where('pgi.item_id', $params['goods'][0]['item_id'])
            ->where('pgi.presell_id', $params['presell_id'])
            ->value('price');
    }

    /**
     * @notes 获取商品售价
     * @param $params
     * @return mixed
     * @author cjhao
     * @date 2022/5/7 18:40
     */
    public static function getGoodsSellPrice($params)
    {
        //会员价允许为零
        if($params['member_price'] >= 0 && $params['member_price'] <= $params['sell_price']){
            return $params['member_price'];
        }
        return $params['sell_price'];
    }

    /**
     * @notes 获取砍价活动价格
     * @param $params
     * @return mixed|void
     * @author Tab
     * @date 2021/10/9 11:30
     */
    public static function getBargainActivityPrice($params)
    {
        $bargainInitiate = BargainInitiate::findOrEmpty($params['initiate_id'])->toArray();
        // 任意金额可购买
        if ($params['buy_condition'] == 'random') {
            return $bargainInitiate['current_price'];
        }
        // 底价购买
        if ($params['buy_condition'] == 'floor') {
            return $bargainInitiate['floor_price'];
        }
    }

    /**
     * @notes 获取秒杀活动价格
     * @param $params
     * @return mixed
     * @author Tab
     * @date 2021/10/9 16:00
     */
    public static function getSeckillActivityPrice($params)
    {
        return SeckillGoodsItem::where([
            'seckill_id' => $params['seckill_id'],
            'item_id' => $params['goods'][0]['item_id'],
        ])->value('seckill_price');
    }

    /**
     * @notes 获取拼团活动价格
     * @param $params
     * @author Tab
     * @date 2021/10/9 16:31
     */
    public static function getTeamActivityPrice($params)
    {
        return TeamGoodsItem::where([
            'team_id' => $params['team_id'],
            'item_id' => $params['goods'][0]['item_id'],
        ])->value('team_price');
    }

    /**
     * @notes 砍价下单后的操作
     * @param $params
     * @param $order
     * @author Tab
     * @date 2021/10/9 11:52
     */
    public static function bargainAfter($params, $order)
    {
        // 更新砍价记录
        $bargainInitiate = BargainInitiate::findOrEmpty($params['initiate_id']);
        $bargainInitiate->order_id = $order['id'];
        // 标识砍价成功避免任意金额可下单的情况用户继续发起帮助砍价
        $bargainInitiate->status = BargainEnum::STATUS_SUCCESS;
        $bargainInitiate->save();
    }

    /**
     * @notes 拼团下单后的操作
     * @param $params
     * @param $order
     * @author Tab
     * @date 2021/10/9 16:35
     */
    public static function teamAfter($params, $order)
    {
        $time = time();
        $news_found_id = null;
        $teamActivity = TeamActivity::findOrEmpty($params['team_id'])->toArray();
        $teamGoods = TeamGoods::where([
            'team_id' => $params['team_id'],
            'goods_id' => $params['goods'][0]['goods_id'],
        ])->findOrEmpty()->toArray();
        $teamGoodsItem = TeamGoodsItem::where([
            'team_id' => $params['team_id'],
            'item_id' => $params['goods'][0]['item_id'],
        ])->findOrEmpty()->toArray();
        $goodsSnap = $teamGoods['goods_snap'];
        $itemSnap = $teamGoodsItem['item_snap'];
        $goodsInfo = [
            'id' => intval($params['goods'][0]['goods_id']),
            'item_id' => intval($params['goods'][0]['item_id']),
            'spec_value_ids' => $params['goods'][0]['spec_value_ids'],
            'name' => $goodsSnap['name'],
            'image' => $itemSnap['image'] ? FileService::getFileUrl($itemSnap['image']) : FileService::getFileUrl($goodsSnap['image']),
            'spec_value_str' => $itemSnap['spec_value_str'],
            'cost_price' => $teamGoodsItem['sell_price'],
            'sell_price' => $teamGoodsItem['team_price'],
            'total_price' => round($teamGoodsItem['team_price'] * $params['goods'][0]['goods_num'], 2),
            'count' => intval($params['goods'][0]['goods_num']),
            'goods_snap' => $goodsSnap,
        ];

        // 开团
        if (!isset($params['found_id']) || empty($params['found_id'])) {
            $found = TeamFound::create([
                'found_sn' => generate_sn((new TeamFound()), 'found_sn'),
                'team_id' => $params['team_id'],
                'user_id' => $params['user_id'],
                'order_id' => $order['id'],
                'people' => $teamActivity['people_num'],
                'join' => 0,
                'status' => 0,
                'goods_snap' => json_encode([
                    'id' => $params['goods'][0]['goods_id'],
                    'name' => $params['goods'][0]['goods_name'],
                    'image' => $params['goods'][0]['image']
                ], JSON_UNESCAPED_UNICODE),
                'kaituan_time' => $time,
                'invalid_time' => ($teamActivity['effective_time'] * 60) + time()
            ]);
            $news_found_id = $found['id'];
        }

        // 参团
        TeamJoin::create([
            'join_sn' => generate_sn((new TeamJoin()), 'join_sn'),
            'team_id' => $params['team_id'],
            'found_id' => $news_found_id ?: $params['found_id'],
            'identity' => $news_found_id ? 1 : 2,
            'user_id' => $params['user_id'],
            'order_id' => $order['id'],
            'status' => 0,
            'team_snap' => json_encode($teamActivity, JSON_UNESCAPED_UNICODE),
            'goods_snap' => json_encode($goodsInfo, JSON_UNESCAPED_UNICODE),
            'invalid_time' => ($teamActivity['effective_time'] * 60) + time(),
            'create_time' => $time,
            'update_time' => $time
        ]);

        // 更新数据
        TeamFound::update(['join' => ['inc', 1]], ['id' => $news_found_id ?: $params['found_id']]);
        TeamActivity::update(['partake_number' => ['inc', 1]], ['id' => $params['team_id']]);
        Order::update(['team_found_id' => $news_found_id ?: $params['found_id']], ['id' => $order['id']]);
    }

    /**
     * @notes 秒杀下单后操作
     * @param $params
     * @param $order
     * @author Tab
     * @date 2021/10/13 14:49
     */
    public static function seckillAfter($params, $order)
    {
        Order::update([
            'id' => $order['id'],
            'seckill_id' => $params['seckill_id']
        ]);
    }
    
    /**
     * @notes 预售下单后操作
     * @param $params
     * @param $order
     * @return void
     * @author lbzy
     * @datetime 2024-04-26 16:43:00
     */
    static function presellAfter($params, $order)
    {
        Order::update([
            'id'            => $order['id'],
            'presell_id'    => $params['presell_id']
        ]);
    }
}