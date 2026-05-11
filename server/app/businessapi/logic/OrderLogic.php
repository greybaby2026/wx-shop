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
namespace app\businessapi\logic;

use app\common\enum\AfterSaleEnum;
use app\common\enum\AfterSaleLogEnum;
use app\common\enum\DeliveryEnum;
use app\common\enum\GoodsEnum;
use app\common\enum\NoticeEnum;
use app\common\enum\OrderEnum;
use app\common\enum\OrderLogEnum;
use app\common\enum\PayEnum;
use app\common\enum\TeamEnum;
use app\common\enum\VerificationEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\logic\PayNotifyLogic;
use app\common\model\AddressLibrary;
use app\common\model\AfterSale;
use app\common\model\AfterSaleGoods;
use app\common\model\Delivery;
use app\common\model\DeliveryTime;
use app\common\model\Express;
use app\common\model\Order;
use app\common\model\OrderGoods;
use app\common\model\OrderLog;
use app\common\model\Region;
use app\common\model\SelffetchShop;
use app\common\model\Verification;
use app\common\service\after_sale\AfterSaleService;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\RegionService;
use app\shopapi\logic\TeamLogic;
use expressage\Kd100;
use expressage\Kdniao;
use think\facade\Db;

/**
 * 订单逻辑类
 * Class OrderLogic
 * @package app\businessapi\logic
 */
class OrderLogic  extends BaseLogic
{
    /**
     * @notes 订单详情
     * @param $params
     * @return array
     * @author 段誉
     * @date 2021/8/2 20:59
     */
    public function detail($params)
    {
        $result = (new Order())->with(['order_goods' => function ($query) {
            $query->field([
                'id', 'order_id', 'goods_id', 'item_id', 'goods_snap',
                'goods_name', 'goods_price', 'goods_num', 'total_price', 'total_pay_price'
            ])->append(['goods_image', 'spec_value_str','original_price'])->hidden(['goods_snap']);
        },'user'])
            ->where(['id' => $params['id']])
            ->append(['businesse_btn', 'delivery_address', 'cancel_unpaid_orders_time', 'show_pickup_code','order_terminal_desc','pay_way_desc','delivery_type_desc'])
            ->hidden(['user_id', 'order_terminal', 'transaction_id', 'delete_time', 'update_time'])
            ->findOrEmpty()->toArray();

        //订单类型
        $result['order_type_desc'] = ($result['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? '自提订单' : OrderEnum::getOrderTypeDesc($result['order_type']);

        //订单状态描述
        $result['order_status_desc'] = ($result['order_status'] == OrderEnum::STATUS_WAIT_DELIVERY && $result['delivery_type'] == DeliveryEnum::SELF_DELIVERY) ? '待取货' : OrderEnum::getOrderStatusDesc($result['order_status']);
        if ($result['order_type'] == OrderEnum::TEAM_ORDER && $result['is_team_success'] != TeamEnum::TEAM_FOUND_SUCCESS) {
            $result['order_status_desc'] = ($result['order_status'] == OrderEnum::STATUS_WAIT_DELIVERY) ? TeamEnum::getStatusDesc($result['is_team_success']) : OrderEnum::getOrderStatusDesc($result['order_status']);
        }

        //自提门店
        $result['selffetch_shop'] = SelffetchShop::where('id', $result['selffetch_shop_id'])
            ->field('id,name,province,city,district,address,business_start_time,business_end_time')
            ->append(['detailed_address'])
            ->hidden(['province', 'city', 'district', 'address'])
            ->find();

        //地址  省市区分隔开
        $result['address']->province = Region::where('id', $result['address']->province)->value('name');
        $result['address']->city = Region::where('id', $result['address']->city)->value('name');
        $result['address']->district = Region::where('id', $result['address']->district)->value('name');

        //订单商品原价总价
        $result['total_original_price'] = 0;
        $result['verification_time'] = '';
        if(DeliveryEnum::SELF_DELIVERY == $result['delivery_type']){
            $time = Verification::where(['order_id' => $result['id']])->value('create_time');
            $time && $result['verification_time'] = date('Y-m-d H:i:s',);
        }
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

            $goods['total_original_price'] = $goods['original_price'] * $goods['goods_num'];
            $result['total_original_price'] += $goods['original_price'] * $goods['goods_num'];
        }
        $result['total_original_price'] = round($result['total_original_price'],2);

        //快递包裹信息
        $result['express_parcel'] = Delivery::field('id,order_id,order_goods_info,express_name,invoice_no,send_type,remark,create_time')
            ->where(['order_id'=>$result['id']])
            ->append(['send_type_desc'])
            ->select()->toArray();

        return $result;
    }


    /**
     * @notes 修改发货地址
     * @param $params
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2023/2/17 10:20
     */
    public static function addressEdit($params)
    {
        Db::startTrans();
        try {
            $order = Order::find($params['id']);

            $address = [
                'contact'   => $params['contact'] ?? $order['address']->contact,
                'province'  => $params['province'],
                'city'      => $params['city'],
                'district'  => $params['district'],
                'address'   => $params['address'],
                'mobile'    => $params['mobile'] ?? $order['address']->mobile,
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
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }

    }

    /**
     * @notes 取消订单
     * @param $params
     * @return bool
     * @author cjhao
     * @date 2023/2/17 9:48
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
            
            if (! $order['businesse_btn']['confirm_pay_btn']) {
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
                    if ($express_type === 'express_bird') {
                        $expressage = (new Kdniao($express_bird['ebussiness_id'], $express_bird['app_key']));
                        $express_field = 'codebird';
                    } elseif ($express_type === 'express_hundred') {
                        $expressage = (new Kd100($express_hundred['customer'], $express_hundred['app_key']));
                        $express_field = 'code100';
                    }
                    //快递编码
                    $express_code = Express::where('id', $parcel['express_id'])->value($express_field);
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
     * @notes 核销订单列表
     * @param $params
     * @return Order|array|string|\think\Model
     * @author cjhao
     * @date 2023/2/17 11:42
     */
    public function getVerificationOrder($params)
    {
        $order = (new Order())
            ->where(['pickup_code' => $params['code']])
            ->with(['order_goods' => function ($query) {
                $query->field('goods_id,order_id,goods_snap,goods_name,goods_price,goods_num,is_comment,original_price')
                    ->append(['goods_image', 'spec_value_str'])
                    ->hidden(['goods_snap']);
            }])
            ->append(['consignee'])
            ->hidden(['address'])
            ->field(['id', 'sn', 'user_id', 'order_type', 'order_status', 'total_num', 'order_amount', 'delivery_type', 'pay_status', 'address', 'create_time','verification_status'])
            ->order(['id' => 'desc'])
            ->findOrEmpty();
        if($order->isEmpty()) {
            return '订单不存在';
        }
        return $order->toArray();
    }


    /**
     * @notes 订单核销
     * @param $params
     * @return bool|string[]
     * @author cjhao
     * @date 2023/2/17 11:58
     */
    public function verification($params)
    {
        try {
            //校验是否有售后
            if ($params['confirm'] != 1) {
                $order_goods = OrderGoods::where(['order_id'=>$params['id']])->select()->toArray();
                foreach ($order_goods as $goods) {
                    $after_sale = AfterSale::where(['order_goods_id' => $goods['id'], 'order_id' => $goods['order_id'],'status'=>[AfterSaleEnum::STATUS_ING,AfterSaleEnum::STATUS_SUCCESS]])->findOrEmpty();
                    if (!$after_sale->isEmpty()) {
                        return ['msg'=>'有商品处于售后中或已售后成功，请谨慎操作'];
                    }
                }
            }

            $order = Order::find($params['id']);

            //添加核销记录
            $snapshot = [
                'sn' => $params['admin_info']['account'],
                'name' => $params['admin_info']['name']
            ];
            $verification = new Verification;
            $verification->order_id = $params['id'];
            $verification->selffetch_shop_id = $order['selffetch_shop_id'];
            $verification->handle_id = $params['admin_info']['admin_id'];
            $verification->verification_scene = VerificationEnum::TYPE_ADMIN;
            $verification->snapshot = json_encode($snapshot);
            $verification->save();

            //更新订单状态
            $order->order_status = OrderEnum::STATUS_FINISH;
            $order->verification_status = OrderEnum::WRITTEN_OFF;
            $order->confirm_take_time = time();
            $order->after_sale_deadline = self::getAfterSaleDeadline();
            $order->save();

            //订单日志
            (new OrderLog())->record([
                'type' => OrderLogEnum::TYPE_SHOP,
                'channel' => OrderLogEnum::SHOP_VERIFICATION,
                'order_id' => $params['id'],
                'operator_id' => $params['admin_info']['admin_id'],
            ]);

            return true;

        } catch (\Exception $e) {
            //错误
            self::$error = $e->getMessage();
            return false;
        }

    }

    /**
     * @notes 获取地址
     * @param $params
     * @author cjhao
     * @date 2023/2/17 17:36
     */
    public function getAddress($params)
    {

        $orderAddress = Order::where(['id'=>$params['id']])->value('address');
        $orderAddress = json_decode($orderAddress,true);
        $orderAddress['province_name'] = Region::where('id', $orderAddress['province'])->value('name');
        $orderAddress['city_name'] = Region::where('id', $orderAddress['city'])->value('name');
        $orderAddress['district_name'] = Region::where('id', $orderAddress['district'])->value('name');
        return $orderAddress;

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
            $order = Order::find($params['id']);
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
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2023/2/20 11:33
     */
    public function getDeliverInfo($params)
    {
        $info = Order::field('id,order_type,address,delivery_content,pay_way,sn,pay_time')
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

        //发货地址、退货地址信息
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

}