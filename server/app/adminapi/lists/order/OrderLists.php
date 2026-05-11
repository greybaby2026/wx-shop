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

namespace app\adminapi\lists\order;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\OrderEnum;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsExtendInterface;
use app\common\model\Order;
use app\common\service\FileService;

class OrderLists extends BaseAdminDataLists implements ListsExtendInterface,ListsExcelInterface
{
    /**
     * @notes 查看订单列表
     * @return array
     * @author ljj
     * @date 2021/8/6 11:25 上午
     */
    public function lists(): array
    {
        $lists = (new Order)::withTrashed()->alias('o')
            ->join('user u','o.user_id = u.id')
            ->join('order_goods og','o.id = og.order_id')
            ->field('o.id,o.sn,o.order_type,o.order_amount,o.address,o.pay_status,o.order_status,o.create_time,u.id as user_id,u.nickname,u.sn as user_sn,u.avatar,o.delivery_type,o.verification_status,o.express_status,o.order_type,o.is_team_success,o.change_price,o.pay_way,o.user_remark,o.order_remarks')
            ->order('o.id','desc')
            ->append(['order_type_desc','pay_status_desc','order_status_desc','admin_order_btn','delivery_type_desc','delivery_address'])
            ->hidden(['pay_status','order_status','delivery_type','verification_status'])
            ->with(['order_goods' => function($query){
                $query->field('id,goods_id,order_id,goods_snap,goods_name,goods_price,goods_num,original_price,change_price,express_price,discount_price,total_price,member_price')
                    ->append(['goods_image','spec_value_str','original_price','after_sale_status_desc','goods_bar_code','code'])
                    ->hidden(['goods_snap']);
            }])
            ->withSearch(array_diff(array_keys($this->params), ['page_no', 'page_size', 'start_time', 'end_time', 'page_size', 'export', 'file_name', 'page_type', 'page_start', 'page_end']), $this->params)
            ->limit($this->limitOffset, $this->limitLength)
            ->group('o.id')
            ->select()
            ->toArray();

        foreach ($lists as &$list) {
            //获取收件人
            $list['contact'] = $list['address']->contact ?? '';
            $list['mobile'] = $list['address']->mobile ?? '';
            unset($list['address']);

            //增加订单商品信息，用于导出
            unset($val);
            $goodsNameArr = [];
            $goodsItemArr = [];
            $goodsNumArr = [];
            $goodsPriceArr = [];
            $expressPriceArr = [];
            $discountAmountArr = [];
            $memberDiscountArr = [];
            $changePriceArr = [];
            $afterStatusArr = [];
            $goodsOriginalPriceArr = [];
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
                $expressPriceArr[] = $val['express_price'];
                $discountAmountArr[] = $val['discount_price'];
                $memberDiscountArr[] = $memberDiscount;
                $changePriceArr[] = $val['change_price'];
                $afterStatusArr[] = $val['after_sale_status_desc'];
                $goodsOriginalPriceArr[] = $val['original_price'];
                $goodsCodeArr[] = $val['code'];
                $goodsBarCodeArr[] = $val['goods_bar_code'];
            }
            $list['goods_name_arr'] = $goodsNameArr;
            $list['goods_item_arr'] = $goodsItemArr;
            $list['goods_num_arr'] = $goodsNumArr;
            $list['goods_price_arr'] = $goodsPriceArr;
            $list['express_price_arr'] = $expressPriceArr;
            $list['discount_amount_arr'] = $discountAmountArr;
            $list['member_discount_arr'] = $memberDiscountArr;
            $list['change_price_arr'] = $changePriceArr;
            $list['after_status_arr'] = $afterStatusArr;
            $list['goods_original_price_arr'] = $goodsOriginalPriceArr;
            $list['goods_code_arr'] = $goodsCodeArr;
            $list['goods_bar_code_arr'] = $goodsBarCodeArr;

            //拼团订单显示拼团状态
            if(OrderEnum::TEAM_ORDER == $list['order_type'] && 1 != $list['is_team_success']){
                0 == $list['is_team_success'] ? $tips = '（拼团中）' : $tips = '（拼团失败）';
                $list['order_type_desc'] .=$tips;
            }
        }

        return $lists;
    }

    /**
     * @notes 查看订单列表总数
     * @return int
     * @author ljj
     * @date 2021/8/4 3:23 下午
     */
    public function count(): int
    {
        return (new Order)::withTrashed()->alias('o')
            ->join('user u','o.user_id = u.id')
            ->join('order_goods og','o.id = og.order_id')
            ->withSearch(array_diff(array_keys($this->params), ['page_no', 'page_size', 'start_time', 'end_time', 'export', 'file_name', 'page_type', 'page_start', 'page_end']), $this->params)
            ->group('o.id')
            ->count();
    }

    /**
     * @notes 统计订单数据
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2021/8/4 7:51 下午
     */
    public function extend(): array
    {
        $lists = (new Order)::withTrashed()->alias('o')
            ->join('user u','o.user_id = u.id')
            ->join('order_goods og','o.id = og.order_id')
            ->withSearch(array_diff(array_keys($this->params), ['page_no', 'page_size', 'start_time', 'end_time','order_status', 'export', 'file_name', 'page_type', 'page_start', 'page_end']), $this->params)
            ->group('o.id')
            ->field('o.id,o.order_status')
            ->select()
            ->toArray();

        $data['all_count'] = 0;
        $data['pay_count'] = 0;
        $data['delivery_count'] = 0;
        $data['receive_count'] = 0;
        $data['finish_count'] = 0;
        $data['close_count'] = 0;
        foreach ($lists as $val) {
            $data['all_count'] += 1;

            if ($val['order_status'] == 0) {
                $data['pay_count'] += 1;
            }
            if ($val['order_status'] == 1) {
                $data['delivery_count'] += 1;
            }
            if ($val['order_status'] == 2) {
                $data['receive_count'] += 1;
            }
            if ($val['order_status'] == 3) {
                $data['finish_count'] += 1;
            }
            if ($val['order_status'] == 4) {
                $data['close_count'] += 1;
            }
        }
        return $data;
    }

    /**
     * @notes 设置导出字段
     * @return string[]
     * @author ljj
     * @date 2021/8/5 7:02 下午
     */
    public function setExcelFields(): array
    {
        return [
            // '数据库字段名(支持别名) => 'Excel表字段名'
            'sn'                => '订单编号',
            'order_type_desc'   => '订单类型',
            'delivery_type_desc'=> '配送方式',
            'create_time'       => '下单时间',
            'nickname'          => '用户名称',
            'goods_code_arr'    => '商品编码',
            'goods_name_arr'    => '商品名称',
            'goods_item_arr'    => '商品sku',
            'goods_bar_code_arr'=> '条形码',
            'goods_original_price_arr'   => '商品单价',
            'goods_num_arr'     => '商品数量',
            'goods_price_arr'   => '商品总额',
            'express_price_arr' => '运费金额',
            'discount_amount_arr'=> '优惠金额',
            'member_discount_arr'=> '会员折扣',
            'change_price_arr'  => '商品改价',
            'order_amount'      => '实付金额',
            'pay_status_desc'   => '支付状态',
            'order_status_desc' => '订单状态',
            'after_status_arr'  => '售后状态',
            'contact'           => '收货人',
            'mobile'            => '手机号码',
            'delivery_address'  => '收货地址',
            'user_remark'       => '买家留言',
            'order_remarks'     => '商家留言',
        ];
    }

    /**
     * @notes 设置默认表名
     * @return string
     * @author ljj
     * @date 2021/8/5 7:02 下午
     */
    public function setFileName(): string
    {
        return '订单列表';
    }
}