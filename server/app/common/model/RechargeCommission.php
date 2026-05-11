<?php

namespace app\common\model;

use think\model\concern\SoftDelete;

class RechargeCommission extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    public function getEarningsAttr($value)
    {
        return clear_zero($value);
    }

    public function getRechargeAmountAttr($value)
    {
        return clear_zero($value);
    }

    public function getRatioAttr($value)
    {
        return clear_zero($value);
    }

    public function getLevelDescAttr($value, $data)
    {
        $desc = [
            1 => '一级',
            2 => '二级',
        ];
        return $desc[$data['level']] ?? '';
    }

    public function getStatusDescAttr($value, $data)
    {
        $desc = [
            1 => '已结算',
        ];
        return $desc[$data['status']] ?? '';
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')
            ->field('id,sn,nickname,avatar,mobile');
    }

    public function fromUser()
    {
        return $this->hasOne(User::class, 'id', 'from_user_id')
            ->field('id,sn,nickname,avatar,mobile');
    }

    public function rechargeOrder()
    {
        return $this->hasOne(RechargeOrder::class, 'id', 'recharge_order_id')
            ->field('id,sn,order_amount,pay_status,pay_time');
    }
}
