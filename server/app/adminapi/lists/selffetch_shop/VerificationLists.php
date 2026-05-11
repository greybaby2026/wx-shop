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

namespace app\adminapi\lists\selffetch_shop;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\DeliveryEnum;
use app\common\enum\OrderEnum;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\Order;
use app\common\model\Verification;

class VerificationLists extends BaseAdminDataLists implements ListsExcelInterface
{
    /**
     * @notes 查看上门自提订单列表
     * @return array
     * @author ljj
     * @date 2021/8/12 11:37 上午
     */
    public function lists(): array
    {
        $lists = (new Order)::alias('o')
            ->join('user u','o.user_id = u.id')
            ->join('order_goods og','o.id = og.order_id')
            ->field('o.id,o.sn,o.order_type,o.order_amount,o.address,o.pay_status,u.nickname,u.avatar,o.delivery_type,o.verification_status,o.create_time,o.order_status,o.selffetch_shop_id,o.is_team_success,o.user_remark,o.order_remarks,og.goods_price')
            ->order('o.id','desc')
            ->append(['order_type_desc','admin_order_btn','order_status_desc','delivery_type_desc','verification_status_desc','pay_status_desc'])
            ->hidden(['delivery_type'])
            ->with(['order_goods' => function($query){
                $query->field('id,goods_id,order_id,goods_snap,goods_name,goods_price,goods_num,original_price,change_price,express_price,discount_price,total_price,member_price')
                    ->append(['goods_image','spec_value_str','original_price','after_sale_status_desc','goods_bar_code','code'])
                    ->hidden(['goods_snap']);
            },'selffetch_shop' => function($query){
                $query->field('id,province,city,district,address')->append(['detailed_address'])->hidden(['goods_snap']);
            },'verification' => function($query){
                $query->field('id,order_id,snapshot,create_time')->json(['snapshot'],true);
            }])
            ->withSearch(array_diff(array_keys($this->params), ['page_no', 'page_size', 'start_time', 'end_time', 'export', 'file_name', 'page_type', 'page_start', 'page_end']), $this->params)
            ->where(['o.delivery_type'=>DeliveryEnum::SELF_DELIVERY])
            ->limit($this->limitOffset, $this->limitLength)
            ->group('o.id')
            ->select()
            ->toArray();

        foreach ($lists as &$list) {
            //获取收件人
            $list['contact'] = $list['address']->contact;
            $list['mobile'] = $list['address']->mobile;
            unset($list['address']);

            //处理订单操作按钮
            unset($list['admin_order_btn']['delete_btn']);
            unset($list['admin_order_btn']['deliver_btn']);
            unset($list['admin_order_btn']['confirm_btn']);
            unset($list['admin_order_btn']['logistics_btn']);
            unset($list['admin_order_btn']['refund_btn']);
            unset($list['admin_order_btn']['refund_detail_btn']);
            unset($list['admin_order_btn']['print_btn']);
            unset($list['admin_order_btn']['remark_btn']);
            unset($list['admin_order_btn']['cancel_btn']);

            //获取核销员&核销时间
            $list['verifier_name'] = '-';
            $list['verification_time'] = '-';
            if ($list['verification_status'] == OrderEnum::WRITTEN_OFF) {
                $list['verification_time'] = $list['verification']['create_time'];
                $list['verifier_name'] = $list['verification']['snapshot']['name'];
            }

            //增加订单商品信息，用于导出
            $goodsNameArr = [];
            $goodsItemArr = [];
            $goodsNumArr = [];
            $goodsPriceArr = [];
            $goodsSellPriceArr = [];
            $expressPriceArr = [];
            $discountAmountArr = [];
            $memberDiscountArr = [];
            $changePriceArr = [];
            $afterStatusArr = [];
            $goodsCodeArr = [];
            $goodsBarCodeArr = [];
            foreach ($list['order_goods'] as $val) {

                //会员折扣
                $memberDiscount = 0;
                if($val['member_price'] > 0){
                    $memberDiscount = round(($val['original_price'] - $val['member_price']) * $val['goods_num'],2);
                }

                $goodsNameArr[] = $val['goods_name'];
                $goodsItemArr[] = $val['spec_value_str'];
                $goodsNumArr[] = $val['goods_num'];
                $goodsPriceArr[] = round($val['original_price'] * $val['goods_num'],2);
                $goodsSellPriceArr[] = $val['original_price'];
                $expressPriceArr[] = $val['express_price'];
                $discountAmountArr[] = $val['discount_price'];
                $memberDiscountArr[] = $memberDiscount;
                $changePriceArr[] = $val['change_price'];
                $afterStatusArr[] = $val['after_sale_status_desc'];
                $goodsCodeArr[] = $val['code'];
                $goodsBarCodeArr[] = $val['goods_bar_code'];
            }
            $list['goods_name_arr'] = $goodsNameArr;
            $list['goods_item_arr'] = $goodsItemArr;
            $list['goods_num_arr'] = $goodsNumArr;
            $list['goods_sell_price_arr'] = $goodsSellPriceArr;
            $list['goods_price_arr'] = $goodsPriceArr;
            $list['express_price_arr'] = $expressPriceArr;
            $list['discount_amount_arr'] = $discountAmountArr;
            $list['member_discount_arr'] = $memberDiscountArr;
            $list['change_price_arr'] = $changePriceArr;
            $list['after_status_arr'] = $afterStatusArr;
            $list['goods_code_arr'] = $goodsCodeArr;
            $list['goods_bar_code_arr'] = $goodsBarCodeArr;
            //拼团订单显示拼团状态
            if(OrderEnum::TEAM_ORDER == $list['order_type'] && 1 != $list['is_team_success']){
                0 == $list['is_team_success'] ? $tips = '（拼团中）' : $tips = '（拼团失败）';
                $list['order_type_desc'] .=$tips;
            }

            $list['shop_detailed_address'] = $list['selffetch_shop']['detailed_address'] ?? '';
        }
        return $lists;
    }

    /**
     * @notes 查看上门自提订单总数
     * @return int
     * @author ljj
     * @date 2021/8/12 11:38 上午
     */
    public function count(): int
    {
        return (new Order)::alias('o')
            ->join('user u','o.user_id = u.id')
            ->join('order_goods og','o.id = og.order_id')
            ->withSearch(array_diff(array_keys($this->params), ['page_no', 'page_size', 'start_time', 'end_time','order_status', 'export', 'file_name', 'page_type', 'page_start', 'page_end']), $this->params)
            ->where(['o.delivery_type'=>DeliveryEnum::SELF_DELIVERY])
            ->group('o.id')
            ->count();
    }

    /**
     * @notes 设置导出字段
     * @return string[]
     * @author ljj
     * @date 2021/8/26 4:43 下午
     */
    public function setExcelFields(): array
    {
        return [
            // '数据库字段名(支持别名) => 'Excel表字段名'
            'sn'                    => '订单编号',
            'order_type_desc'       => '订单类型',
            'delivery_type_desc'    => '配送方式',
            'create_time'           => '下单时间',
            'nickname'              => '用户名称',
            'goods_code_arr'        => '商品编码',
            'goods_name_arr'        => '商品名称',
            'goods_item_arr'        => '商品sku',
            'goods_bar_code_arr'    => '条形码',
            'goods_num_arr'         => '商品数量',
            'goods_sell_price_arr'  => '商品单价',
            'goods_price_arr'       => '商品总额',
            'express_price_arr'     => '运费金额',
            'discount_amount_arr'   => '优惠金额',
            'member_discount_arr'=> '会员折扣',
            'change_price_arr'      => '商品改价',
            'order_amount'          => '实付金额',
            'pay_status_desc'       => '支付状态',
            'order_status_desc'     => '订单状态',
            'after_status_arr'      => '售后状态',
            'verification_status_desc'=> '核销状态',
            'verifier_name'         => '核销员',
            'verification_time'     => '核销时间',
            'contact'               => '收货人',
            'mobile'                => '手机号码',
            'shop_detailed_address' => '自提地址',
            'user_remark'           => '买家留言',
            'order_remarks'         => '商家留言',
        ];
    }

    /**
     * @notes 设置默认表名
     * @return string
     * @author ljj
     * @date 2021/8/26 4:43 下午
     */
    public function setFileName(): string
    {
        return '自提订单列表';
    }
}