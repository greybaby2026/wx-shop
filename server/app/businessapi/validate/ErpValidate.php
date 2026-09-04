<?php
namespace app\businessapi\validate;

use app\common\validate\BaseValidate;

class ErpValidate extends BaseValidate
{
    protected $rule = [
        'mobile'        => 'require|mobile',
        'sms_code'      => 'require|length:4',
        'user_sn'       => 'requireWithout:mobile',
        'goods_list'    => 'require|array|min:1',
        'total_amount'  => 'require|float|gt:0',
        'deduct_ratio'  => 'number|between:0,100',
        'order_id'      => 'require|number',
        'pay_way'       => 'in:offline_cash,offline_card,offline_pos,user_money',
    ];

    protected $message = [
        'mobile.require'     => '请输入手机号',
        'mobile.mobile'      => '手机号格式不正确',
        'sms_code.require'   => '请输入短信验证码',
        'sms_code.length'    => '验证码为4位数字',
        'user_sn.requireWithout' => '请输入手机号或用户编号',
        'goods_list.require'  => '请添加商品',
        'goods_list.min'      => '至少需要一个商品',
        'total_amount.require' => '请输入订单金额',
        'total_amount.gt'     => '订单金额必须大于0',
        'deduct_ratio.between' => '抵扣比例在0-100之间',
        'order_id.require'    => '缺少订单ID',
    ];

    public function sceneSendCode()
    {
        return $this->only(['mobile']);
    }

    public function sceneUserInfo()
    {
        return $this->only(['user_sn', 'mobile']);
    }

    public function sceneCreateOrder()
    {
        return $this->only(['mobile', 'sms_code', 'goods_list', 'total_amount', 'deduct_ratio', 'remark', 'pay_way']);
    }

    public function sceneConfirmPay()
    {
        return $this->only(['order_id', 'pay_way']);
    }
}
