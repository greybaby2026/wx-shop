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
namespace app\shopapi\logic;

use app\common\enum\AfterSaleEnum;
use app\common\enum\OrderEnum;
use app\common\enum\WithdrawEnum;
use app\common\logic\BaseLogic;
use app\common\model\AfterSale;
use app\common\model\ChatRelation;
use app\common\model\Distribution;
use app\common\model\FootprintRecord;
use app\common\model\GoodsComment;
use app\common\model\LuckyDrawRecord;
use app\common\model\Order;
use app\common\model\SelffetchVerifier;
use app\common\model\User;
use app\common\model\UserAuth;
use app\common\model\UserSession;
use app\common\model\WithdrawApply;
use app\common\service\ConfigService;
use think\facade\Db;

/**
 * @notes 用户注销
 * author lbzy
 * @datetime 2023-08-21 16:24:13
 * @class UserDeleteLogic
 * @package app\shopapi\logic
 */
class UserDeleteLogic extends BaseLogic
{
    /**
     * @notes 检测是否可注销
     * @param $user_id
     * @return int[]
     * @author lbzy
     * @datetime 2023-08-21 16:37:38
     */
    static function checkCanDelete($user_id) : array
    {
        $order_status = [
            OrderEnum::STATUS_WAIT_PAY,
            OrderEnum::STATUS_WAIT_DELIVERY,
            OrderEnum::STATUS_WAIT_RECEIVE,
        ];
        
        $after_status = [
            AfterSaleEnum::STATUS_ING,
        ];
        
        $withdraw_status = [
            WithdrawEnum::STATUS_ING,
            WithdrawEnum::STATUS_WAIT,
        ];
    
        $status     = User::where('id', $user_id)->value('disable') == 0 ? 1 : 0;
        $order      = Order::where('user_id', $user_id)->where('order_status', 'IN', $order_status)->value('id') ? 0 : 1;
        $after_sale = AfterSale::where('user_id', $user_id)->where('status', 'IN', $after_status)->value('id') ? 0 : 1;
        $withdraw   = WithdrawApply::where('user_id', $user_id)->where('status', 'in', $withdraw_status)->value('id') ? 0 : 1;
        
        $result = [
            'data'  => [
                // 是否冻结
                'status'       => [
                    'pass'          => $status,
                    'msg'           => $status ? '通过' : '账号冻结中，无法申请注销',
                ],
                // 是否有未完成订单
                'order'        => [
                    'pass'          => $order,
                    'msg'           => $order ? '通过' : '存在未完成订单，无法申请注销',
                ],
                // 是否有售后处理中
                'after_sale'   => [
                    'pass'          => $after_sale,
                    'msg'           => $after_sale ? '通过' : '存在售后订单，无法申请注销',
                ],
                // 提现申请
                'withdraw'     => [
                    'pass'          => $withdraw,
                    'msg'           => $withdraw ? '通过' : '存在佣金待提现申请，无法申请注销',
                ],
            ],
            'pass'  => 1,
            'msg'   => '通过',
        ];
    
        foreach ($result['data'] as $info) {
            if ($info['pass'] == 0) {
                $result['pass'] = 0;
                $result['msg']  = $info['msg'];
                break;
            }
        }
        
        return $result;
    }
    
    /**
     * @notes 确定注销
     * @param $user_id
     * @return bool|string
     * @author lbzy
     * @datetime 2023-08-21 16:47:13
     */
    static function sureDelete($user_id) : bool|string
    {
        $check = static::checkCanDelete($user_id);
        
        if ($check['pass'] == 0) {
            return $check['msg'];
        }
        
        try {
            Db::startTrans();
            
            // 用户数据
            User::update([
                'user_delete'       => 1,
                'disable'           => 1,
    
                'account'           => '',
                'password'          => '',
                'pay_password'      => '',
                'mobile'            => '',
    
                'first_leader'      => 0,
                'second_leader'     => 0,
                'third_leader'      => 0,
                'ancestor_relation' => '',
                'inviter_id'        => 0,
                'code'              => '',
            ], [ [ 'id', '=', $user_id ] ]);
            
            // 用户openid unionid
            $userAuths = UserAuth::where('user_id', $user_id)->select()->toArray();
            foreach ($userAuths as $userAuth) {
                UserAuth::update([
                    'openid'    => "{$user_id}-{$userAuth['openid']}-" . time(),
                    'unionid'   => "{$user_id}-{$userAuth['unionid']}-" . time(),
                ], [ [ 'id', '=', $userAuth['id'] ] ]);
            }
            
            // 用户token
            UserSession::destroy(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });
            
            // 用户客服
            ChatRelation::destroy(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });
            
            // 用户分销关系清除
            User::update([ 'first_leader' => 0, 'second_leader' => 0, 'third_leader' => 0 ], [ [ 'first_leader', '=', $user_id ] ]);
            User::update([ 'second_leader' => 0, 'third_leader' => 0 ], [ [ 'second_leader', '=', $user_id ] ]);
            User::update([ 'third_leader' => 0 ], [ [ 'third_leader', '=', $user_id ] ]);
            User::update([ 'inviter_id' => 0 ], [ [ 'inviter_id', '=', $user_id ] ]);
            
            // 分销冻结
            Distribution::update([
                'is_distribution'       => 0,
                'is_freeze'             => 1,
            ], [ [ 'user_id', '=', $user_id ] ]);
            
            // 核销员
            SelffetchVerifier::destroy(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });
            
            // 足迹气泡
            FootprintRecord::destroy(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });
            
            // 幸运转盘活动
            LuckyDrawRecord::destroy(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });
            
            // 商品评论
            GoodsComment::destroy(function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            });
            
            
            Db::commit();
            return true;
        } catch(\Throwable $e) {
            static::setError($e->getMessage());
            Db::rollback();
            return $e->getMessage();
        }
    }
}