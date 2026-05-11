<?php

namespace app\adminapi\validate\marketing;

class RegisterAwardValidate extends \app\common\validate\BaseValidate
{
    protected $rule = [
        
        'status'                => [ 'require', 'in' => [ 0, 1 ] ],
        
        'user_integral_status'   => [ 'require', 'in' => [ 0, 1 ], 'check_integral_status' ],
        'user_integral_num'      => [ ],
        
        'user_money_status' => [ 'require', 'in' => [ 0, 1 ] ],
        'user_money_num'    => [ ],
        
        'coupon_status'     => [ 'require', 'in' => [ 0, 1 ] ],
        'coupon_array'      => [ ],
        
        'style'             => [ 'require', 'in' => [ 1, 2 ] ],
    ];
    
    protected $field = [
        'status'                        => '状态',
        'user_integral_status'           => '积分',
        'user_integral_num'              => '积分数量',
        'user_money_status'         => '余额',
        'user_money_num'            => '余额数量',
        'coupon_status'             => '优惠券',
        'coupon_array'              => '优惠券列表',
        'style'                     => '活动弹窗模版',
    ];
    
    protected $message  = [
        'user_money_num.number' => '余额数量必须是整数',
        'user_integral_num.number' => '积分数量必须是整数',
    ];
    
    function sceneSet()
    {
        $appends = [];
    
        $integral_status    = input('user_integral_status/d');
        $user_money_status  = input('user_money_status/d');
        $coupon_status      = input('coupon_status/d');
        
        if ($integral_status == 1) {
            $appends['user_integral_num']    = [ 'require', 'number', 'gt' => 0 ];
        }
        if ($user_money_status == 1) {
            $appends['user_money_num']  = [ 'require', 'number', 'gt' => 0 ];
        }
        if ($coupon_status == 1) {
            $appends['coupon_array']    = [ 'require', 'array', 'checkCouponArray' ];
        }
        
        return $this->only([
            'status',
            'user_integral_status',
            'user_integral_num',
            'user_money_status',
            'user_money_num',
            'coupon_status',
            'coupon_array',
            'style',
        ])->append($appends);
    }
    
    function checkCouponArray($couponArray, $rule, $data)
    {
        foreach ($couponArray as $couponItem) {
            if (! is_array($couponItem)) {
                return '优惠券列表 格式错误';
            }
            if (! isset($couponItem['id'])) {
                return '优惠券列表 id不能为空';
            }
        }
        
        return true;
    }
    
    function check_integral_status($integral_status, $rule, $data)
    {
        if ($integral_status != 1 && input('user_money_status') != 1 && input('coupon_status') != 1) {
            return '注册奖励至少选择一项';
        }
        
        return true;
    }
}