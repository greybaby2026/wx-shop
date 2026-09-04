<?php
namespace app\adminapi\validate\user;
use app\common\{
    model\User,
    validate\BaseValidate
};

class adjustUserWallet extends BaseValidate
{
    protected $rule = [
        'user_id'   => 'require',
        'type'      => 'require|in:1,2,3,4',
        'action'    => 'require|in:0,1',
        'num'       => 'require|gt:0|checkNum',
        'remark'    => 'max:128',
    ];
    protected $message = [
        'id.require'        => '请选择用户',
        'type.require'      => '请选择变动类型',
        'type.in'           => '变动类型错误',
        'action.require'    => '请选择调整类型',
        'action.in'         => '调整类型错误',
        'num.require'       => '请输入调整数量',
        'num.gt'            => '调整余额必须大于零',
        'remark'            => '备注不可超过128个符号',
    ];

    protected function checkNum($vaule, $rule, $data)
    {
        $user = User::find($data['user_id']);
        if (empty($user)) {
            return '用户不存在';
        }
        if ($user['user_delete']) {
            return '用户已注销，不能操作';
        }
        if (1 == $data['action']) {
            return true;
        }
        switch ($data['type']) {
            case 1:
                $surplusMoney = $user->user_money - $vaule;
                if ($surplusMoney < 0) {
                    return '用户可用余额仅剩' . $user->user_money;
                }
                break;
            case 2:
                $surplusMoney = $user->user_earnings - $vaule;
                if ($surplusMoney < 0) {
                    return '用户可提现金额仅剩' . $user->user_earnings;
                }
                break;
            case 3:
                $surplusIntegral = $user->user_integral - $vaule;
                if ($surplusIntegral < 0) {
                    return '用户积分仅剩' . $user->user_earnings;
                }
                break;
            case 4:
                $surplusActivity = $user->activity_money - $vaule;
                if ($surplusActivity < 0) {
                    return '用户活动余额仅剩' . $user->activity_money;
                }
                break;
        }
        return true;
    }
}
