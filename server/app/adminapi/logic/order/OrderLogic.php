<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------

namespace app\adminapi\logic\order;


use app\common\cache\YlyPrinterCache;
use app\common\enum\AfterSaleEnum;
use app\common\enum\AfterSaleLogEnum;
use app\common\enum\DeliveryEnum;
use app\common\enum\GoodsEnum;
use app\common\enum\NoticeEnum;
use app\common\enum\OrderEnum;
use app\common\enum\OrderLogEnum;
use app\common\enum\UserTerminalEnum;
use app\common\enum\PayEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\logic\PayNotifyLogic;
use app\common\model\AddressLibrary;
use app\common\model\AfterSale;
use app\common\model\Delivery;
use app\common\model\DeliveryTime;
use app\common\model\Express;
use app\common\model\GoodsSupplier;
use app\common\model\Order;
use app\common\model\OrderGoods;
use app\common\model\OrderLog;
use app\common\model\SelffetchShop;
use app\common\service\after_sale\AfterSaleService;
use app\common\service\ConfigService;
use app\common\service\printer\YlyPrinterService;
use app\common\service\RegionService;
use app\common\service\WechatMiniExpressSendSyncService;
use app\shopapi\logic\TeamLogic;
use expressage\Kd100;
use expressage\Kdniao;
use think\Exception;
use think\facade\Db;

class OrderLogic extends BaseLogic
{
    /**
     * @notes 查看其他列表
     * @return array
     * @author ljj
     * @date 2021/8/5 10:09 上午
     */
    public function otherLists()
    {
        $other_lists = [
            'order_terminal_lists' => UserTerminalEnum::getTermInalDesc(true),
            'order_type_lists' => OrderEnum::getOrderTypeDesc(true),
            'pay_way_lists' => PayEnum::getPayDesc(true),
            'pay_status_lists' => PayEnum::getPayStatusDesc(true),
            'delivery_type_lists' => DeliveryEnum::getDeliveryTypeDesc(true),
            'refund_status_lists' => [],
        ];

        return $other_lists;
    }

    /**
     * @notes 查看订单详情
     * @param $params
     * @return mixed
     * @author ljj
     * @date 2021/8/9 5:27 下午
     */
    public function detail($params)
    {
        $info = Order::withTrashed()->alias('o')
            ->join('user u', 'o.user_id = u.id')
            ->leftjoin('selffetch_shop ss', 'ss.id = o.selffetch_shop_id')
            ->leftjoin('delivery d', 'd.id = o.delivery_id')
            ->leftjoin('verification v', 'v.order_id = o.id')
            ->where('o.id',$params['id'])
            ->with(['order_goods' => function($query){
                $query->field('id,order_id,goods_id,goods_snap,goods_name,goods_price,goods_num,total_price,discount_price as coupon_discount,member_price,integral_price,change_price,total_pay_price,original_price,express_price')->append(['goods_image','spec_value_str','code','supplier_name']);
            },'order_log' => function($query){
                $query->field('order_id,type,operator_id,channel,create_time')->append(['operator','channel_desc'])->hidden(['operator_id','channel'])->order('id','desc');
            },'selffetch_shop' => function($query){
                $query->field('id,name,contact,mobile,province,city,district,address')->append(['detailed_address'])->hidden(['province','city','district','address']);
            }])
            ->field('o.id,o.order_status,o.sn,o.order_type,o.order_terminal,o.create_time,o.pay_status,o.pay_way,o.pay_time,confirm_take_time,u.id as user_id,
            u.sn as user_sn,u.nickname,o.address,o.express_status,o.delivery_type,o.express_time,o.express_again,o.user_remark,o.order_remarks,
            o.discount_amount,o.member_amount,o.change_price,o.express_price,o.order_amount,o.integral_amount,o.is_team_success,d.express_name,d.invoice_no,
            o.pickup_code,v.create_time as verification_time,o.verification_status,o.delivery_content,o.delivery_content1,o.delivery_content_type,d.send_type,o.selffetch_shop_id')
            ->append(['order_status_desc','order_type_desc','order_terminal_desc','pay_status_desc','pay_way_desc','delivery_address','express_status_desc','delivery_type_desc','admin_order_btn'])
            ->find()
            ->toArray();
        //显示供应商名称
        $goodsIds = array_column($info['order_goods'],'goods_id');
        $goodsSupplier = GoodsSupplier::alias('GS')
                        ->join('goods G','G.supplier_id = GS.id')
                        ->where(['G.id'=>$goodsIds])
                        ->column('GS.name','G.id');

        $totalDiscount = 0;
        $goodsAmount = 0;
        foreach ($info['order_goods'] as $key => $orderGoods){
            $info['order_goods'][$key]['supplier_name'] = $goodsSupplier[$orderGoods['goods_id']] ?? '';
            $info['order_goods'][$key]['member_discount'] = 0;
            if($orderGoods['member_price'] > 0){
                $info['order_goods'][$key]['member_discount'] = round(($orderGoods['original_price'] - $orderGoods['member_price']) * $orderGoods['goods_num'],2);
            }
            unset($info['order_goods'][$key]['member_price']);

            $info['order_goods'][$key]['integral_discount'] = round($orderGoods['integral_price'] * $orderGoods['goods_num'],2);
            unset($info['order_goods'][$key]['integral_price']);
            $info['order_goods'][$key]['total_discount'] = round($info['order_goods'][$key]['member_discount']+ $info['order_goods'][$key]['coupon_discount'] +$info['order_goods'][$key]['integral_discount'],2);

            $info['order_goods'][$key]['total_amount'] = round($orderGoods['original_price'] * $orderGoods['goods_num'],2);
//            $totalDiscount = round($totalDiscount + $info['order_goods'][$key]['total_discount'],2);
            $goodsAmount += $info['order_goods'][$key]['total_amount'];

            //售后状态
            $info['order_goods'][$key]['after_sale_status_desc'] = '无售后';
            $after_sale = AfterSale::where(['order_goods_id' => $orderGoods['id'], 'order_id' => $orderGoods['order_id']])->findOrEmpty();
            if (!$after_sale->isEmpty()) {
                $info['order_goods'][$key]['after_sale_status_desc'] = AfterSaleEnum::getStatusDesc($after_sale->status);
            }
        }
//        $info['total_discount'] = $totalDiscount;//订单总优惠金额
        $info['total_discount'] = round($info['discount_amount'] + $info['member_amount'] + $info['integral_amount'],2);
        unset($info['discount_amount']);unset($info['member_amount']);unset($info['integral_amount']);
        $info['total_goods_amount'] = round($goodsAmount,2);//订单商品总价
        //TODO 计算订单商品实付总额(订单商品总价-优惠券金额-积分抵扣金额-商品改价)
//        $info['total_goods_pay_price'] = round($info['total_goods_price'] - $info['discount_amount'] - $info['integral_amount'] - $info['change_price'],2);
//        if ($info['total_goods_pay_price'] < 0) {
//            $info['total_goods_pay_price'] = 0;
//        }

        //收货信息
        $info['contact'] = $info['address']->contact;
        $info['mobile'] = $info['address']->mobile;
        unset($info['address']);

        //拼团订单显示拼团状态
        if(OrderEnum::TEAM_ORDER == $info['order_type'] && 1 != $info['is_team_success']){
            0 == $info['is_team_success'] ? $tips = '（拼团中）' : $tips = '（拼团失败）';
            $info['order_type_desc'] .=$tips;
        }

        //提货时间
        $info['verification_time'] = empty($info['verification_time']) ? '-' : date('Y-m-d H:i:s',$info['verification_time']);

        //快递包裹信息
        $info['express_parcel'] = Delivery::field('id,order_id,order_goods_info,express_name,invoice_no,send_type,remark,create_time')
            ->where(['order_id'=>$info['id']])
            ->append(['send_type_desc'])
            ->select()->toArray();

        return $info;
    }

    /**
     * @notes 修改地址
     * @param $params
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2021/8/10 11:34 上午
     */
    public function addressEdit($params)
    {
        $order = Order::find($params['id']);

        $address = [
            'contact'   => $params['contact'],
            'mobile'    => $params['mobile'],
            'province'  => $params['province_id'],
            'city'      => $params['city_id'],
            'district'  => $params['district_id'],
            'address'   => $params['address'],
        ];
        $order->address = $address;
        $order->save();

        $change_address= RegionService::getAddress(
            [
                $params['province_id'] ?? '',
                $params['city_id'] ?? '',
                $params['district_id'] ?? ''
            ],
            $params['address'] ?? '',
        );

        //订单日志
        (new OrderLog())->record([
            'type' => OrderLogEnum::TYPE_SHOP,
            'channel' => OrderLogEnum::SHOP_ADDRESS_EDIT,
            'order_id' => $params['id'],
            'operator_id' => $params['admin_id'],
            'content' => '商家修改收货地址为：'.$change_address,
        ]);

        return true;
    }

    /**
     * @notes 设置商家备注
     * @param $params
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2021/8/10 11:49 上午
     */
    public function orderRemarks($params)
    {
        $order_remarks = $params['order_remarks'] ?? '';
        foreach ($params['id'] as $id) {
            Db::name('order')->where(['id' => $id])->update(['order_remarks' => $order_remarks]);

            //订单日志
            (new OrderLog())->record([
                'type' => OrderLogEnum::TYPE_SHOP,
                'channel' => OrderLogEnum::SHOP_ORDER_REMARKS,
                'order_id' => $id,
                'operator_id' => $params['admin_id'],
                'content' => '商家备注：'.$order_remarks,
            ]);
        }

        return true;
    }

    /**
     * @notes 修改价格(订单详情)
     * @param $params
     * @return bool
     * @author ljj
     * @date 2021/8/10 2:53 下午
     */
    public function changePrice($params)
    {
        // 启动事务
        Db::startTrans();
        try {
            //更新订单商品表
            $order_goods = OrderGoods::find($params['order_goods_id']);
            $order_goods->change_price = $params['change_price'];
    
            $order = Order::findOrEmpty($order_goods['order_id'] ?? 0);
            
            if ($order->order_status != OrderEnum::STATUS_WAIT_PAY) {
                throw new \Exception('订单已支付或者已关闭了，不能再进行改价');
            }
            
            // TODO 实际支付商品金额(商品总价-优惠券金额-积分抵扣的金额-商品改价+运费)
            $order_goods->total_pay_price = $order_goods->total_price - $order_goods->discount_price - $order_goods->integral_price + $params['change_price'] + $order_goods->express_price;
            if ($order_goods->total_pay_price - $order_goods->express_price < 0) {
                $discountPrice =  round($order_goods->total_price - $order_goods->discount_price - $order_goods->integral_price, 2);
                throw new Exception('减少的优惠价格不能大于'. $discountPrice . '元');
            }

            $order_goods->save();

            //更新订单表
            $total_change_price = OrderGoods::where('order_id',$order_goods->order_id)->sum('change_price');
            
            $order->change_price = $total_change_price;
            // TODO 应付款金额(订单商品总价-优惠券金额-积分抵扣金额-商品改价+运费)
            $order->order_amount = $order->goods_price - $order->discount_amount - $order->integral_amount + $total_change_price + $order->express_price;
            if ($order->order_amount < 0) {
                $order->order_amount = 0;
            }
            $order->save();

            //订单日志
            (new OrderLog())->record([
                'type' => OrderLogEnum::TYPE_SHOP,
                'channel' => OrderLogEnum::SHOP_CHANGE_PRICE,
                'order_id' => $order_goods->order_id,
                'operator_id' => $params['admin_id'],
                'content' => '商家修改订单商品【'.$order_goods->goods_name.'】的商品改价为：'.$params['change_price'],
            ]);

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 修改运费(订单详情)
     * @param $params
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2021/8/10 3:11 下午
     */
    public function changeExpressPrice($params)
    {
        // 启动事务
        Db::startTrans();
        try {

            if ($params['express_price'] < 0) {
                throw new Exception('运费最低可改为 0 元');
            }
            
            $order_goods = OrderGoods::find($params['order_goods_id']);
            $order_goods->express_price = $params['express_price'];
            
            $order = Order::findOrEmpty($order_goods['order_id'] ?? 0);
            if ($order->order_status != OrderEnum::STATUS_WAIT_PAY) {
                throw new \Exception('订单已支付或者已关闭了，不能再进行改价');
            }
            
            // TODO 实际支付商品金额(商品总价-优惠券金额-积分抵扣的金额-商品改价+运费)
            $order_goods->total_pay_price = $order_goods->total_price - $order_goods->discount_price - $order_goods->integral_price + $order_goods->change_price + $params['express_price'];
            
            $order_goods->save();
    
            //更新订单表
            $total_express_price = OrderGoods::where('order_id',$order_goods->order_id)->sum('express_price');
            
            $order->express_price = $total_express_price;
            // TODO 订单总价(订单商品总价+运费)
            $order->total_amount = $order->goods_price + $order->express_price;
            // TODO 应付款金额(订单商品总价-优惠券金额-积分抵扣金额-商品改价+运费)
            $order->order_amount = $order->goods_price - $order->discount_amount - $order->integral_amount + $order->change_price + $order->express_price;
            if ($order->order_amount < 0) {
                $order->order_amount = 0;
            }
            
            $order->save();
    
            //订单日志
            (new OrderLog())->record([
                'type' => OrderLogEnum::TYPE_SHOP,
                'channel' => OrderLogEnum::SHOP_EXPRESS_PRICE,
                'order_id' => $order_goods->order_id,
                'operator_id' => $params['admin_id'],
                'content' => '商家修改订单商品【'.$order_goods->goods_name.'】的商品运费为：'.$params['express_price'],
            ]);
    
            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 取消订单
     * @param $params
     * @return bool
     * @author ljj
     * @date 2021/8/10 4:50 下午
     */
    public function cancel($params)
    {
        Db::startTrans();
        try {

            $order = Order::find($params['id']);

            if ($order['order_type'] == OrderEnum::TEAM_ORDER) {

                TeamLogic::signFailTeam($order['id']);

            } else {

                //更新订单表
                $order->order_status = OrderEnum::STATUS_CLOSE;
                $order->cancel_time = time();

                //TODO  处于已支付状态的发起整单售后
                if ($order->pay_status == PayEnum::ISPAID) {
                    AfterSaleService::orderRefund([
                        'order_id' => $params['id'],
                        'scene' => AfterSaleLogEnum::SELLER_CANCEL_ORDER
                    ]);
                }
                $order->save();

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
                    'type' => OrderLogEnum::TYPE_SHOP,
                    'channel' => OrderLogEnum::SHOP_CANCEL_ORDER,
                    'order_id' => $params['id'],
                    'operator_id' => $params['admin_id'],
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
    
    function confirmOfflinePay($params): bool|string
    {
        try {
            $order = Order::where('id', $params['id'])->append([ 'admin_order_btn', 'businesse_btn' ])->findOrEmpty()->toArray();
    
            if (empty($order)) {
                throw new \Exception('订单不存在');
            }
            
            if (! $order['admin_order_btn']['confirm_pay_btn']) {
                throw new \Exception('订单已不能确认线下付款');
            }
    
            PayNotifyLogic::handle('order', $order['sn']);
            
            return true;
        } catch(\Throwable $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 发货
     * @param $params
     * @return bool
     * @author ljj
     * @date 2021/8/10 6:25 下午
     */
    public function delivery($params)
    {
        Db::startTrans();
        try {
            $order = Order::lock(true)->find($params['id']);
            if ($order['order_status'] != OrderEnum::STATUS_WAIT_DELIVERY || !in_array($order['express_status'],[DeliveryEnum::NOT_SHIPPED,DeliveryEnum::PART_SHIPPED])) {
                return '订单不允许发货';
            }
            //消息通知
            $notice[] = [
                'express_name' => '无需快递',
                'invoice_no' => '',
            ];
            //虚拟发货和实物发货
            if(OrderEnum::VIRTUAL_ORDER != $order['order_type']){

                $delivery = new Delivery;
                $deliveryData = [];
                $orderGoodsInfo = [];
                $orderGoodsData = [];

                //新建批次发货时间记录
                $deliveryTime = DeliveryTime::create(['order_id' => $params['id']]);

                //快递发货
                $deliveryAddressInfo = AddressLibrary::where(['id'=>$params['delivery_address_id']])->append(['province','city','district','complete_address'])->findOrEmpty()->toArray();
                $returnAddressInfo = AddressLibrary::where(['id'=>$params['return_address_id']])->append(['province','city','district','complete_address'])->findOrEmpty()->toArray();
                if ($params['send_type'] == DeliveryEnum::EXPRESS) {
                    $parcelOrderGoodsData = [];
                    $notice = [];
                    foreach ($params['parcel'] as $parcel) {
                        //处理包裹数据
                        $orderGoodsInfo = [];
                        foreach ($parcel['order_goods_info'] as $value) {
                            $orderGoodsInfo[] = [
                                'order_goods_id' => $value['order_goods_id'],
                                'delivery_num' => $value['delivery_num']
                            ];
                            $parcelOrderGoodsData[$value['order_goods_id']] = ($parcelOrderGoodsData[$value['order_goods_id']] ?? 0) + $value['delivery_num'];
                        }

                        //添加发货单记录
                        $express_id = $parcel['express_id'];
                        $invoice_no = $parcel['invoice_no'];
                        $express_name = Express::where('id',$express_id)->value('name');
                        $deliveryData[] = [
                            'delivery_time_id' => $deliveryTime->id,
                            'order_id' => $params['id'],
                            'order_sn' => $order['sn'],
                            'order_goods_info' => json_encode($orderGoodsInfo),
                            'user_id' => $order['user_id'],
                            'admin_id' => $params['admin_id'],
                            'delivery_address_info' => json_encode($deliveryAddressInfo),
                            'return_address_info' => json_encode($returnAddressInfo),
                            'contact' => $order['address']->contact,
                            'mobile' => $order['address']->mobile,
                            'province' => $order['address']->province,
                            'city' => $order['address']->city,
                            'district' => $order['address']->district,
                            'address' => $order['address']->address,
                            'express_status' => DeliveryEnum::SHIPPED,
                            'express_id' => $express_id,
                            'express_name' => $express_name,
                            'invoice_no' => $invoice_no,
                            'send_type' => $params['send_type'],
                            'remark' => $params['remark'] ?? '',
                        ];

                        $notice[] = [
                            'express_name' => $express_name,
                            'invoice_no' => $invoice_no,
                        ];
                    }

                    foreach ($parcelOrderGoodsData as $key=>$val) {
                        $orderGoods = OrderGoods::where(['id'=>$key])->field('goods_num,delivery_num')->findOrEmpty()->toArray();
                        $delivery_num = $val + $orderGoods['delivery_num'];
                        $is_shipped = $delivery_num >= $orderGoods['goods_num'] ? true : false;
                        $orderGoodsData[] = [
                            'id' => $key,
                            'delivery_num' => $is_shipped ? $orderGoods['goods_num'] : $delivery_num,
                            'express_status' => $is_shipped ? DeliveryEnum::SHIPPED : DeliveryEnum::PART_SHIPPED,
                        ];
                    }

                } else {
                    //无需快递
                    //添加发货单记录
                    $orderGoods = OrderGoods::where(['id'=>$params['order_goods_ids']])->field('id,goods_num,delivery_num')->select()->toArray();
                    foreach ($orderGoods as $goods) {
                        $delivery_num = $goods['goods_num'] - $goods['delivery_num'];
                        $delivery_num = $delivery_num > 0 ? $delivery_num : 0;
                        $already_delivery_num = $goods['delivery_num'] + $delivery_num;

                        $orderGoodsInfo[] = [
                            'order_goods_id' => $goods['id'],
                            'delivery_num' => $delivery_num
                        ];
                        $orderGoodsData[] = [
                            'id' => $goods['id'],
                            'delivery_num' => $already_delivery_num,
                            'express_status' => $already_delivery_num >= $goods['goods_num'] ? DeliveryEnum::SHIPPED : DeliveryEnum::PART_SHIPPED,
                        ];
                    }
                    $deliveryData[] = [
                        'delivery_time_id' => $deliveryTime->id,
                        'order_id' => $params['id'],
                        'order_sn' => $order['sn'],
                        'order_goods_info' => json_encode($orderGoodsInfo),
                        'user_id' => $order['user_id'],
                        'admin_id' => $params['admin_id'],
                        'delivery_address_info' => json_encode($deliveryAddressInfo),
                        'return_address_info' => json_encode($returnAddressInfo),
                        'contact' => $order['address']->contact,
                        'mobile' => $order['address']->mobile,
                        'province' => $order['address']->province,
                        'city' => $order['address']->city,
                        'district' => $order['address']->district,
                        'address' => $order['address']->address,
                        'express_status' => DeliveryEnum::NOT_SHIPPED,
                        'send_type' => $params['send_type'],
                        'remark' => $params['remark'] ?? '',
                    ];
                }

                //保存发货单记录
                $delivery->saveAll($deliveryData);

                //更新订单商品信息
                (new OrderGoods())->saveAll($orderGoodsData);

                //获取最新订单商品信息
                $orderGoodsIds = OrderGoods::where(['order_id'=>$params['id'],'express_status'=>[DeliveryEnum::NOT_SHIPPED,DeliveryEnum::PART_SHIPPED]])->column('id');
                if (!empty($orderGoodsIds)) {
                    foreach ($orderGoodsIds as $key=>$orderGoodsId) {
                        $afterSale = AfterSale::where(['order_goods_id'=>$orderGoodsId,'status'=>AfterSaleEnum::STATUS_SUCCESS])->findOrEmpty();
                        if (!$afterSale->isEmpty()) {
                            unset($orderGoodsIds[$key]);
                        }
                    }
                }
                $order_status = OrderEnum::STATUS_WAIT_DELIVERY;
                $express_status = DeliveryEnum::PART_SHIPPED;
                if (empty($orderGoodsIds)) {
                    $order_status = OrderEnum::STATUS_WAIT_RECEIVE;
                    $express_status = DeliveryEnum::SHIPPED;
                }
                //更新订单表
                $order->order_status = $order_status;
                $order->express_status = $express_status;
                $order->express_time = time();
                $order->delivery_id = 0;
                $order->save();

            }else{
                $orderGoods = OrderGoods::where(['order_id'=>$order['id']])->find();
                //自动完成订单
                $order->order_status = OrderEnum::STATUS_FINISH;
                if(GoodsEnum::AFTER_DELIVERY_HANDOPERSTION == $orderGoods->goods_snap->after_delivery){
                    $order->order_status = OrderEnum::STATUS_WAIT_RECEIVE;
                }
                //更新订单表
                $order->express_status = DeliveryEnum::NOT_SHIPPED;
                $order->delivery_content = $params['delivery_content'] ?? '';
                $order->delivery_content1 = $params['delivery_content1'] ?? [];
                $order->delivery_content_type = $params['delivery_content_type'] ?? 0;
                $order->express_time = time();
                $order->delivery_id = 0;
                if(GoodsEnum::AFTER_DELIVERY_AUTO == $orderGoods->goods_snap->after_delivery){
                    $order->confirm_take_time = time();
                }
                $order->save();

            }


            //订单日志
            (new OrderLog())->record([
                'type' => OrderLogEnum::TYPE_SHOP,
                'channel' => OrderLogEnum::SHOP_DELIVERY_ORDER,
                'order_id' => $params['id'],
                'operator_id' => $params['admin_id'],
            ]);

            // 消息通知
            foreach ($notice as $value) {
                event('Notice', [
                    'scene_id' => NoticeEnum::ORDER_SHIP_NOTICE,
                    'params' => [
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'express_name' => $value['express_name'],
                        'invoice_no' => $value['invoice_no'],
                        'ship_time' => date('Y-m-d H:i:s')
                    ]
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
     * @notes 发货信息
     * @param $params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2021/8/10 7:16 下午
     */
    public function deliveryInfo($params)
    {
        $info = Order::withTrashed()->field('id,order_type,address,delivery_content,pay_way,sn,pay_time')
            ->with(['order_goods'=> function($query){
                $query->field('id,order_id,goods_snap,goods_name,goods_price,discount_price,goods_num,total_pay_price,delivery_num')
                    ->append(['goods_image','spec_value_str','surplus_delivery_num'])
                    ->where(['express_status'=>[DeliveryEnum::NOT_SHIPPED,DeliveryEnum::PART_SHIPPED]]);
            }])
            ->where('id',$params['id'])
            ->append(['delivery_address'])
            ->find()
            ->toArray();

        //计算单个商品实付价格
        foreach ($info['order_goods'] as $key=>&$val) {
            $val['pay_price'] = $val['total_pay_price'] / $val['goods_num'];


            //售后状态
            $val['after_sale_status_desc'] = '无售后';
            $val['after_sale_status'] = 0;
            $val['after_sale_id'] = 0;
            $after_sale = AfterSale::where(['order_goods_id' => $val['id'], 'order_id' => $val['order_id']])->findOrEmpty();
            if ($after_sale->status == AfterSaleEnum::STATUS_SUCCESS) {
                unset($info['order_goods'][$key]);
                continue;
            }
            if (!$after_sale->isEmpty()) {
                $val['after_sale_status_desc'] = AfterSaleEnum::getStatusDesc($after_sale->status);
                $val['after_sale_status'] = $after_sale->status;
                $val['after_sale_id'] = $after_sale->id;
            }
        }
        $info['order_goods'] = array_values($info['order_goods']);
        if(OrderEnum::VIRTUAL_ORDER == $info['order_type']){
            $info['delivery_content'] = $val['goods_snap']->delivery_content;
        }

        //处理收货信息
        $info['contact'] = $info['address']->contact;
        $info['mobile'] = $info['address']->mobile;
        unset($info['address']);

        //获取物流公司
        $info['express'] = Express::field('id,name,code')->select()->toArray();

        //发货地址、退货地址信息  如果有发过货的，使用上一次发货的地址
        $lastDelivery = Delivery::where(['order_id'=>$info['id']])->field('delivery_address_info,return_address_info')->order(['id'=>'desc'])->findOrEmpty()->toArray();
        $deliveryAddress = !empty($lastDelivery['delivery_address_info']) ? json_decode($lastDelivery['delivery_address_info'],true) : '';
        if(empty($deliveryAddress)) {
            $deliveryAddress = AddressLibrary::order(['is_deliver_default'=>'desc','id'=>'asc'])->append(['province','city','district'])->findOrEmpty()->toArray();
        }
        $returnAddress = !empty($lastDelivery['return_address_info']) ? json_decode($lastDelivery['return_address_info'],true) : '';
        if(empty($returnAddress)) {
            $returnAddress = AddressLibrary::order(['is_return_default'=>'desc','id'=>'asc'])->append(['province','city','district'])->findOrEmpty()->toArray();
        }
        $info['company_address'] = [
            'delivery_address' => $deliveryAddress,
            'return_address' => $returnAddress,
        ];

        return $info;
    }

    /**
     * @notes 确认收货
     * @param $params
     * @return bool
     * @author ljj
     * @date 2021/8/11 10:37 上午
     */
    public function confirm($params)
    {
        // 启动事务
        Db::startTrans();
        try {
            //更新订单状态
            $order = Order::find($params['id']);
            $order->order_status = OrderEnum::STATUS_FINISH;
            $order->confirm_take_time = time();
            $order->after_sale_deadline = self::getAfterSaleDeadline();
            $order->save();

            //订单日志
            (new OrderLog())->record([
                'type' => OrderLogEnum::TYPE_SHOP,
                'channel' => OrderLogEnum::SHOP_CONFIRM_ORDER,
                'order_id' => $params['id'],
                'operator_id' => $params['admin_id'],
            ]);

            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 获取当前售后
     * @return float|int
     * @author ljj
     * @date 2021/9/1 3:09 下午
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
     * @notes 物流查询
     * @param $params
     * @return mixed
     * @author ljj
     * @date 2021/8/13 3:48 下午
     */
    public function logistics($params)
    {
        $result = [];

        //发货批次时间
        $result['delivery_time'] = DeliveryTime::where(['order_id'=>$params['id']])->field('id,order_id,create_time')->select()->toArray();
        if (empty($result['delivery_time'])) {
            //处理旧数据
            $result['delivery_time'][] = [
                'id' => 0,
                'order_id' => $params['id'],
                'create_time' => time()
            ];
        }

        //订单信息
        $order = Order::field('id,sn,pay_time,address,order_type')
            ->append(['delivery_address'])
            ->where('id',$params['id'])
            ->find()
            ->toArray();

        //物流配置
        $express_type = ConfigService::get('logistics_config', 'express_type', '');
        $express_bird = unserialize(ConfigService::get('logistics_config', 'express_bird', ''));
        $express_hundred = unserialize(ConfigService::get('logistics_config', 'express_hundred', ''));

        $deliveryInfo = [];
        foreach ($result['delivery_time'] as $value) {
            $parcelInfo = Delivery::field('id,order_id,order_goods_info,express_name,express_id,invoice_no,send_type,remark,create_time,delivery_address_info,return_address_info')
                ->where(['order_id'=>$order['id'],'delivery_time_id'=>$value['id']])
                ->append(['send_type_desc'])
                ->select()->toArray();
            if (empty($parcelInfo)) {
                //处理旧数据
                $result['delivery_time'] = [];
                continue;
            } else {
                if ($value['id'] == 0) {
                    $result['delivery_time'][0]['create_time'] = $parcelInfo[0]['create_time'];
                }
            }
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
//                    if (in_array(strtolower($express_code), [ 'sf', 'shunfeng' ])) {
//                        if ($express_type === 'express_bird') {
//                            $expressage->logistics($express_code, $parcel['invoice_no'], substr($order['address']->mobile, -4));
//                        } else {
//                            $expressage->logistics($express_code, $parcel['invoice_no'], $order['address']->mobile);
//                        }
//                    }else {
//                        $expressage->logistics($express_code, $parcel['invoice_no']);
//                    }
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
            }
            $deliveryAddressInfo = !empty($parcelInfo[0]['delivery_address_info']) ? json_decode($parcelInfo[0]['delivery_address_info'],true) : '';
            $returnAddressInfo = !empty($parcelInfo[0]['return_address_info']) ? json_decode($parcelInfo[0]['return_address_info'],true) : '';

            $deliveryInfo[] = [
                'delivery_time_id' => $value['id'],
                'order_id' => $order['id'],
                'order_sn' => $order['sn'],
                'order_type' => $order['order_type'],
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
            ];
        }
        $result['delivery_info'] = $deliveryInfo;

        return $result;
    }

    /**
     * @notes 小票打印
     * @param $params
     * @return bool
     * @author Tab
     * @date 2021/11/16 11:16
     */
    public function orderPrint($params)
    {
        try {
            $order = $this->getOrderInfo($params['id']);
            if(! (new YlyPrinterService())->startPrint($order)){
                static::setError('打印机离线');
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return $this->handleCatch($e);
        }
    }

    /**
     * @notes 获取商品信息
     * @param $orderId
     * @return array
     * @throws \Exception
     * @author Tab
     * @date 2021/11/16 11:16
     */
    public function getOrderInfo($orderId)
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
        $order = Order::withTrashed()->field($field)->with(['orderGoods' => function($query) {
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
     * @notes 统一处理涉及易联云的错误
     * @param $e
     * @return false
     * @author Tab
     * @date 2021/11/16 17:30
     */
    public function handleCatch($e)
    {
        $msg = json_decode($e->getMessage(),true);
        if(18 === $e->getCode()){
            //access_token过期，清除缓存中的access_token
            (new YlyPrinterCache())->deleteTag();
        };
        if($msg && isset($msg['error'])){
            self::$error =  '易联云：'.$msg['error_description'];
            return false;
        }

        self::$error = $e->getMessage();
        return false;
    }


    /**
     * @notes 重新修改物流信息
     * @param array $params
     * @return bool|string
     * @author cjhao
     * @date 2022/9/5 14:27
     */
    public function changeDelivery(array $params){
        try{
            if(empty($params['id'])){
                throw new \Exception('请选择发货单');
            }
            if(empty($params['express_id']) && empty($params['invoice_no'])){
                throw new \Exception("请选择物流或者输入要修改的单号");
            }
            $delivery = Delivery::findOrEmpty($params['id']);
            if($delivery->isEmpty()){
                throw new \Exception('发货单不存在');
            }
            if($delivery->send_type == DeliveryEnum::NO_EXPRESS){
                throw new \Exception('发货单无需快递');
            }

            $order = Order::field('id,order_status')->findOrEmpty($delivery->order_id);
            if($order->isEmpty()){
                throw new \Exception('订单不存在');
            }
            if($order->order_status > OrderEnum::STATUS_WAIT_RECEIVE){
                throw new \Exception('订单已完成或已关闭');
            }

            $experssName = Express::where('id',$params['express_id'])->value('name');
            $delivery->express_id = $params['express_id'];
            $delivery->express_name = $experssName;
            $delivery->invoice_no = $params['invoice_no'];
            $delivery->save();
            
//            $order->express_again       = 1;
            $order->express_time        = time();
//            $order->delivery_id         = $expressId ? $delivery->id : 0;
//            $order->delivery_content    = $params['delivery_content'] ?? '';
            $order->save();
    
//            WechatMiniExpressSendSyncService::_sync_order($order->toArray());
        
            return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
}