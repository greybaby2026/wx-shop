<?php

namespace app\common\logic;

use app\adminapi\logic\marketing\CouponLogic;
use app\common\enum\AccountLogEnum;
use app\common\enum\CouponEnum;
use app\common\model\AccountLog;
use app\common\model\Coupon;
use app\common\model\CouponList;
use app\common\model\User;
use app\common\service\ConfigService;
use think\facade\Db;

class RegisterAwardLogic extends BaseLogic
{
    static function getConfig()
    {
        $config = [
            'status'   => ConfigService::get('register_award', 'status', 0),
            
            'user_integral_status'   => ConfigService::get('register_award', 'user_integral_status', 0),
            'user_integral_num'      => ConfigService::get('register_award', 'user_integral_num', ''),
            
            'user_money_status' => ConfigService::get('register_award', 'user_money_status', 0),
            'user_money_num'    => ConfigService::get('register_award', 'user_money_num', ''),
            
            'coupon_status'     => ConfigService::get('register_award', 'coupon_status', 0),
            'coupon_array'      => (array) ConfigService::get('register_award', 'coupon_array', []),
            
            // 1：风格1 2：风格2
            'style'             => ConfigService::get('register_award', 'style', 1),
        ];
    
        $hidden = [
            'send_total_type', 'send_total',
            'use_time_type', 'use_time_start', 'use_time_end', 'use_time',
            'get_type', 'get_num_type', 'get_num',
            'use_goods_ids',
            'create_time', 'update_time', 'delete_time',
        ];
        
        $cIds = [];
        foreach ($config['coupon_array'] as $key => $item) {
            if (isset($item['id'])) {
                $cIds[] = $item['id'];
            } else {
                unset($config['coupon_array'][$key]);
            }
        }
        
        if ($cIds) {
            $lists = Coupon::field(true)
                ->where('id', 'in', array_column($config['coupon_array'], 'id'))
                ->where([ 'status' => CouponEnum::COUPON_STATUS_CONDUCT ])
                ->append([ 'is_available', 'is_receive', 'condition', 'is_empty', 'use_time_text', 'use_time_text2', 'discount_content', 'use_time_text', 'use_time_text2' ])
                ->hidden($hidden)
                ->withAttr('is_empty', function ($value, $data) {
                    // 判断优惠券是否库存已空
                    unset($value);
                    if ($data['send_total_type'] == CouponEnum::SEND_TOTAL_TYPE_FIXED) {
                        if ($data['send_total'] <= 0) {
                            return 1;
                        }
                        $receiveTotal = (new CouponList())->where(['coupon_id'=>intval($data['id'])])->count();
                        if ($receiveTotal >= $data['send_total']) {
                            return 1;
                        }
                    }
                    return 0;
                })
                ->order('id', 'desc')
                ->select()
                ->toArray();
            
            $lists = array_column($lists, null, 'id');
            
            foreach ($config['coupon_array'] as $key => $item) {
                $config['coupon_array'][$key] = $lists[$item['id']] ?? $item;
            }
        }
        
        return $config;
    }
    
    static function setConfig(array $params)
    {
    
        ConfigService::set('register_award', 'status', $params['status'] ?? 0);
        
        ConfigService::set('register_award', 'user_integral_status', $params['user_integral_status'] ?? 0);
        ConfigService::set('register_award', 'user_integral_num', $params['user_integral_num'] ?? 0);
        
        ConfigService::set('register_award', 'user_money_status', $params['user_money_status'] ?? 0);
        ConfigService::set('register_award', 'user_money_num', $params['user_money_num'] ?? 0);
        
        ConfigService::set('register_award', 'coupon_status', $params['coupon_status'] ?? 0);
        ConfigService::set('register_award', 'coupon_array', (array) ($params['coupon_array'] ?? []));
        
        ConfigService::set('register_award', 'style', $params['style'] ?? 1);
        
        return true;
    }
    
    static function registerAward($userId)
    {
        $config = static::getConfig();
        
        // 未开启注册额奖励
        if (! $config['status']) {
            return true;
        }
        
        if ($config['user_integral_status'] == 1 && $config['user_integral_num'] > 0) {
            User::update([
                'id'            => $userId,
                'user_integral' => Db::raw('user_integral+' . $config['user_integral_num']),
            ]);
            AccountLogLogic::add($userId, AccountLogEnum::INTEGRAL_INC_USER_REGISTER, AccountLogEnum::INC, $config['user_integral_num']);
        }
        if ($config['user_money_status'] == 1 && $config['user_money_num'] > 0) {
            User::update([
                'id'            => $userId,
                'user_money' => Db::raw('user_money+' . $config['user_money_num']),
            ]);
            AccountLogLogic::add($userId, AccountLogEnum::BNW_INC_USER_REGISTER, AccountLogEnum::INC, $config['user_money_num']);
        }
        if ($config['coupon_status'] == 1) {
            foreach ($config['coupon_array'] as $coupon) {
                CouponLogic::send([
                    'id'            => $coupon['id'],
                    'send_user_num' => 1,
                    'send_user'     => [ $userId ],
                ]);
            }
        }
    }
}