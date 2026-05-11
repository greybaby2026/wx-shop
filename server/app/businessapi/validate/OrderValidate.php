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


namespace app\businessapi\validate;


use app\common\enum\AfterSaleEnum;
use app\common\enum\DeliveryEnum;
use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\enum\TeamEnum;
use app\common\enum\YesNoEnum;
use app\common\model\AfterSale;
use app\common\model\Express;
use app\common\model\Order;
use app\common\model\OrderGoods;
use app\common\service\ConfigService;
use app\common\validate\BaseValidate;
use think\facade\Validate;

/**
 * 订单验证
 * Class OrderValidate
 * @package app\shopapi\validate
 */
class OrderValidate extends BaseValidate
{

    protected $rule = [
        'id'            => 'require|checkOrder',
        'code'          => 'require',
        'province'      => 'require',
        'city'          => 'require',
        'distric'       => 'require',
        'address'       => 'require',
        'confirm'       => 'require|in:0,1',
        'delivery_address_id' => 'require',
        'return_address_id' => 'require',
        'parcel' => 'requireIf:send_type,1|array',
        'order_goods_ids' => 'require|array'
    ];


    protected $message = [
        'id.require'            => '参数缺失',
        'code.require'          => '请输入核销码',
        'province.require'      => '所选地区不能为空',
        'city.require'          => '请选择完整地址',
        'distric.require'       => '请选择完整地址',
        'address.require'       => '详细地址不能为空',
        'confirm.require'       => '参数缺失',
        'confirm.in'            => '参数错误',
        'delivery_address_id.require'   => '请选择发货地址',
        'return_address_id.require'   => '请选择退货地址',
        'parcel.requireIf'   => '请输入包裹信息',
        'parcel.array'   => '包裹错误',
        'order_goods_ids.require' => '请选择发货商品',
        'order_goods_ids.array' => '发货商品错误',
    ];


    public function sceneId()
    {
        return $this->only(['id']);
    }

    public function sceneCancel()
    {
        return $this->only(['id'])
            ->remove('id','checkOrder')
            ->append('id','checkCancel');
    }

    public function sceneOrderTraces()
    {
        return $this->only(['id'])->append('id','checkTraces');

    }

    public function sceneCode()
    {
        return $this->only(['code']);
    }

    public function sceneConfirm()
    {
        return $this->only(['id'])
            ->append('id','checkConfirm');
    }

    public function sceneVerification()
    {
        return $this->only(['id','confirm'])
            ->append('id','checkVerification');
    }

    public function sceneDelivery()
    {
        return $this->only(['id'])
            ->append('id','checkDelivery');
    }

    public function sceneAddressEdit()
    {
        return $this->only(['id','province','city','district','address'])
            ->append('id','checkAddressEdit');
    }




    //验证订单
    public function checkOrder($value, $rule, $data)
    {
        $order = (new Order())->getOrderById($value);

        if ($order->isEmpty()) {
            return '订单不存在';
        }

        return true;
    }


    /**
     * @notes 检查订单是否可以发货
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2021/8/10 5:59 下午
     */
    public function checkDelivery($value,$rule,$data)
    {
        $order = Order::where('id', $value)->findOrEmpty();
        if(OrderEnum::VIRTUAL_ORDER == $order['order_type']){
            if (empty($data['delivery_content']) && empty($data['delivery_content1'])) {
                return '发货内容不能为空';
            }
            if (! in_array($data['delivery_content_type'] ?? -111, [ 0, 1 ])) {
                return '发货类型不能为空';
            }
            return true;
        }

        $validate = Validate::rule([
            'send_type|配送方式' => 'require|in:1,2',
            'delivery_address_id|发货地址' => 'require',
            'return_address_id|退货地址' => 'require',
            'parcel|包裹' => 'requireIf:send_type,1|array',
            'order_goods_ids|发货商品' => 'require|array'
        ]);
        if (!$validate->check($data)) {
            return $validate->getError();
        }

        if ($order['order_type'] == OrderEnum::TEAM_ORDER && $order['is_team_success'] != TeamEnum::TEAM_FOUND_SUCCESS) {
            return '该订单正在拼团中还不能发货';
        }

        if ($order['order_status'] == OrderEnum::STATUS_CLOSE) {
            return '订单已关闭';
        }

        if ($order['order_status'] != OrderEnum::STATUS_WAIT_DELIVERY || !in_array($order['express_status'],[DeliveryEnum::NOT_SHIPPED,DeliveryEnum::PART_SHIPPED])) {
            return '订单不允许发货';
        }
        if(!isset($data['send_type']) || !in_array($data['send_type'],[1,2])){
            return '发货类型错误';
        }
        if(1 == $data['send_type']){
//            if((!isset($data['invoice_no'])  || empty($data['invoice_no']))) {
//                return '请输入单号';
//            }
//            if(!isset($data['express_id']) || empty($data['express_id'])){
//                return '请选择物流公司';
//            }
//            $this->checkExpressId($data['express_id'],[],[]);
            $invoiceNoArr = [];
            foreach ($data['parcel'] as $parcel) {
                if(!isset($parcel['invoice_no']) || empty($parcel['invoice_no']) || !isset($parcel['express_id']) || empty($parcel['express_id'])){
                    return '包裹运单信息不完整，请检查后再发货';
                }
                if(!isset($parcel['order_goods_info']) || empty($parcel['order_goods_info']) || !is_array($parcel['order_goods_info'])){
                    return '包裹商品信息错误，请检查后再发货';
                }
                foreach ($parcel['order_goods_info'] as $info) {
                    if(!isset($info['order_goods_id']) || empty($info['order_goods_id']) || !isset($info['delivery_num']) || empty($info['delivery_num'])){
                        return '包裹商品信息缺失，请检查后再发货';
                    }
                }

                if (in_array($parcel['invoice_no'], $invoiceNoArr)) {
                    return '多个包裹运单号重复';
                }
                $invoiceNoArr[] = $parcel['invoice_no'];

                $order_goods_ids = array_column($parcel['order_goods_info'],'order_goods_id');
                $delivery_num_arr = array_column($parcel['order_goods_info'],'delivery_num','order_goods_id');
                $order_goods = OrderGoods::where(['id'=>$order_goods_ids])->select()->toArray();
                foreach ($order_goods as $goods) {
                    if ($goods['express_status'] == DeliveryEnum::SHIPPED) {
                        return '存在已发货商品，无法发货';
                    }
                    if ($goods['goods_num'] - $goods['delivery_num'] < $delivery_num_arr[$goods['id']]) {
                        return '超出商品剩余数量，无法发货';
                    }
                }
                $after_sale = AfterSale::where(['order_goods_id' => $order_goods_ids, 'order_id' => $data['id']])->findOrEmpty();
                if (!$after_sale->isEmpty() &&  in_array($after_sale->status,[AfterSaleEnum::STATUS_ING,AfterSaleEnum::STATUS_SUCCESS])) {
                    return '存在售后商品，无法发货';
                }
            }
        } else {
            $order_goods = OrderGoods::where(['id'=>$data['order_goods_ids']])->select()->toArray();
            foreach ($order_goods as $goods) {
                if ($goods['express_status'] == DeliveryEnum::SHIPPED) {
                    return '存在已发货商品，无法发货';
                }
            }
            $after_sale = AfterSale::where(['order_goods_id' => $data['order_goods_ids'] ?? 0, 'order_id' => $data['id']])->findOrEmpty();
            if (!$after_sale->isEmpty() &&  in_array($after_sale->status,[AfterSaleEnum::STATUS_ING,AfterSaleEnum::STATUS_SUCCESS])) {
                return '存在售后商品，无法发货';
            }
        }

//        $order_goods = OrderGoods::where(['order_id'=>$order['id']])->select()->toArray();
//        foreach ($order_goods as $goods) {
//            $after_sale = AfterSale::where(['order_goods_id' => $goods['id'], 'order_id' => $goods['order_id']])->findOrEmpty();
//            if (!$after_sale->isEmpty() && $after_sale->status == AfterSaleEnum::STATUS_ING) {
//                return '订单商品：'.$goods['goods_name'].' 处于售后中，无法发货';
//            }
//        }


        return true;
    }


    /**
     * @notes 检查订单是否可以修改地址
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2021/8/10 11:37 上午
     */
    public function checkAddressEdit($value,$rule,$data)
    {
        $order = Order::where('id', $value)->findOrEmpty();
        if ($order['express_status'] == 1) {
            return '订单已发货，不可以修改地址';
        }
        return true;
    }

    /**
     * @notes 检查物流公司是否存在
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2021/8/10 5:29 下午
     */
    public function checkExpressId($value,$rule,$data)
    {
        $order = Express::where('id', $value)->findOrEmpty();
        if ($order->isEmpty()) {
            return '物流公司不存在';
        }
        return true;
    }



    /**
     * @notes 检查订单是否已发货
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2021/8/13 11:20 上午
     */
    public function checkLogistics($value,$rule,$data)
    {
        $order = Order::where('id', $value)->findOrEmpty();
        if ($order['express_status'] == DeliveryEnum::NOT_SHIPPED) {
            return '订单未发货，暂无物流信息';
        }
        return true;
    }


    /**
     * @notes 验证订单能否取消
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author 段誉
     * @date 2021/8/2 15:48
     */
    public function checkCancel($value, $rule, $data)
    {
        $order = Order::where('id', $value)->findOrEmpty();
        if ($order->isEmpty()) {
            return '订单不存在';
        }
        if ($order['order_status'] != OrderEnum::STATUS_WAIT_PAY && $order['order_status'] != OrderEnum::STATUS_WAIT_DELIVERY && ($order['order_status'] == OrderEnum::STATUS_WAIT_RECEIVE && $order['delivery_type'] != DeliveryEnum::SELF_DELIVERY)) {
            return '订单不允许取消';
        }

        return true;
    }


    /**
     * @notes 验证能否确认收货
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author 段誉
     * @date 2021/8/2 15:26
     */
    public function checkConfirm($value, $rule, $data)
    {
        $order = (new Order())->getOrderById($value);

        if ($order->isEmpty()) {
            return '订单不存在';
        }

        if ($order['order_status'] < OrderEnum::STATUS_WAIT_RECEIVE) {
            return '订单未发货';
        }

        if ($order['order_status'] == OrderEnum::STATUS_FINISH) {
            return '订单已完成';
        }

        return true;
    }

    /**
     * @notes 检查订单是否有物流信息
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2021/8/13 3:56 下午
     */
    public function checkTraces($value, $rule, $data)
    {
        $order = (new Order())->getOrderById($value);

        if ($order->isEmpty()) {
            return '订单不存在';
        }

        if ($order['express_status'] == DeliveryEnum::NOT_SHIPPED) {
            return '订单未发货，暂无物流信息';
        }

        return true;
    }

    /**
     * @notes 检查订单是否可以删除
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2021/8/31 2:36 下午
     */
    public function checkDel($value, $rule, $data)
    {
        $order = Order::where('id',$value)->find()->toArray();

        if ($order['order_status'] != OrderEnum::STATUS_CLOSE) {
            return '订单无法删除';
        }
        //如果在售后中无法删除
        if(AfterSale::where(['order_id'=>$value,'status'=>AfterSaleEnum::STATUS_ING])->find()){
            return '订单正在售后中，无法删除';
        }

        return true;
    }


    /***
     * @notes 核销订单
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author cjhao
     * @date 2023/2/17 11:55
     */
    public function checkVerification($value,$rule,$data)
    {
        $result = Order::where('id', $value)->findOrEmpty();
        if ($result['pay_status'] != PayEnum::ISPAID) {
            return '订单未支付，不允许核销';
        }
    
        if ($result['order_status'] == OrderEnum::STATUS_CLOSE) {
            return '订单已关闭';
        }
        
        if ($result['delivery_type'] != DeliveryEnum::SELF_DELIVERY) {
            return '非自提订单，不允许核销';
        }
        if ($result['verification_status'] == OrderEnum::WRITTEN_OFF) {
            return '订单已核销';
        }
        if ($result['order_type'] == OrderEnum::TEAM_ORDER){
            if ($result['is_team_success'] != TeamEnum::TEAM_FOUND_SUCCESS){
                return '拼团成功后才能核销';
            }
        }
    
        $order_goods = OrderGoods::where(['order_id'=>$result['id']])->select()->toArray();
        foreach ($order_goods as $goods) {
            $after_sale = AfterSale::where(['order_goods_id' => $goods['id'], 'order_id' => $goods['order_id']])->findOrEmpty();
            if (!$after_sale->isEmpty() && $after_sale->status == AfterSaleEnum::STATUS_ING) {
                return '订单商品：'.$goods['goods_name'].' 处于售后中，无法发货';
            }
        }
        
        return true;
    }

}