<?php
// +----------------------------------------------------------------------
// | LikeShop有特色的全开源社交分销电商系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 商业用途务必购买系统授权，以免引起不必要的法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | 微信公众号：好象科技
// | 访问官网：http://www.likemarket.net
// | 访问社区：http://bbs.likemarket.net
// | 访问手册：http://doc.likemarket.net
// | 好象科技开发团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | Author: LikeShopTeam
// +----------------------------------------------------------------------

namespace app\shopapi\logic;

use app\common\enum\AccountLogEnum;
use app\common\enum\UserTerminalEnum;
use app\common\enum\WithdrawEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\model\User;
use app\common\model\WithdrawApply;
use app\common\service\ConfigService;
use app\common\service\WeChatConfigService;
use think\facade\Db;

/**
 * 提现逻辑层
 * Class WithdrawLogic
 * @package app\shopapi\logic
 */
class WithdrawLogic extends BaseLogic
{
    /**
     * @notes 获取提现配置
     * @param $userId
     * @return array
     * @author Tab
     * @date 2021/8/6 17:06
     */
    public static function getConfig($userId,$terminal)
    {
        $user = User::findOrEmpty($userId)->toArray();
        $config = [
            'able_withdraw' => $user['user_earnings'],
            'min_withdraw' => ConfigService::get('config', 'withdraw_min_money', WithdrawEnum::DEFAULT_MIN_MONEY),
            'max_withdraw' => ConfigService::get('config', 'withdraw_max_money', WithdrawEnum::DEFAULT_MAX_MONEY),
            'percentage' => ConfigService::get('config', 'withdraw_service_charge', WithdrawEnum::DEFAULT_PERCENTAGE),
        ];

        $types = ConfigService::get('config', 'withdraw_way', WithdrawEnum::DEFAULT_TYPE);
        foreach($types as $value) {
            //h5和头条隐藏微信零钱提现
            if(in_array($terminal,[UserTerminalEnum::H5,UserTerminalEnum::TOUTIAO]) && WithdrawEnum::TYPE_WECHAT_CHANGE == $value){
                continue;
            }
            $config['type'][] = [
                'name' => WithdrawEnum::getTypeDesc($value),
                'value' => $value
            ];
        }
        return $config;
    }

    /**
     * @notes 提现申请
     * @param $params
     * @return bool
     * @author Tab
     * @date 2021/8/6 17:52
     */
    public static function apply($params)
    {
        Db::startTrans();
        try {
            $maxSingle = ConfigService::get('config', 'withdraw_max_single', 0);
            if ($maxSingle > 0 && $params['money'] > $maxSingle) {
                throw new \Exception('单笔提现金额不能超过' . $maxSingle . '元');
            }

            $maxDaily = ConfigService::get('config', 'withdraw_max_daily', 0);
            if ($maxDaily > 0) {
                $todayTotal = WithdrawApply::where('user_id', $params['user_id'])
                    ->whereTime('create_time', 'today')
                    ->where('status', 'in', [WithdrawEnum::STATUS_WAIT, WithdrawEnum::STATUS_ING, WithdrawEnum::STATUS_SUCCESS])
                    ->sum('money');
                if (bcadd($todayTotal, $params['money'], 2) > $maxDaily) {
                    throw new \Exception('今日提现总额已达上限');
                }
            }

            // 手续费,单位：元
            $handlingFee = 0;
            if($params['type'] != WithdrawEnum::TYPE_BALANCE) {
                // 不是提现至余额，需收手续费
                $percentage = ConfigService::get('config', 'withdraw_service_charge', WithdrawEnum::DEFAULT_PERCENTAGE);
                $handlingFee = bcmul(bcdiv($params['money'], 100, 4), $percentage, 2);
            }

            $withdrawApply = new WithdrawApply();
            $data = [
                'sn' => generate_sn($withdrawApply, 'sn'),
                'batch_no' => generate_sn($withdrawApply, 'batch_no','SJZZ'),
                'user_id' => $params['user_id'],
                'real_name' => $params['real_name'] ?? '',
                'account' => $params['account'] ?? '',
                'type' => $params['type'],
                'money' => $params['money'],
                'left_money' => bcsub($params['money'], $handlingFee, 2),
                'money_qr_code' => $params['money_qr_code'] ?? '',
                'handling_fee' => $handlingFee,
                'apply_remark' => $params['apply_remark'] ?? '',
                'status' => WithdrawEnum::STATUS_WAIT,
                'bank' => $params['bank'] ?? '',
                'subbank' => $params['subbank'] ?? '',
                'terminal' => request()->userInfo['terminal'],
            ];
            // 新增提现申请记录
            $withdrawApply->save($data);

            // 扣减用户可提现金额
            $user = User::find($params['user_id']);
            $user->user_earnings = $user->user_earnings - $params['money'];
            $user->save();

            // 添加账户流水记录
            AccountLogLogic::add($user->id, AccountLogEnum::BW_DEC_WITHDRAWAL, AccountLogEnum::DEC, $params['money'], $withdrawApply->sn, '提现申请');

            Db::commit();
            return $withdrawApply->id;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 查看提现申请详情
     * @param $params
     * @return array
     * @author Tab
     * @date 2021/8/6 19:06
     */
    public static function detail($params)
    {
        $field = 'id,status,status as status_desc,money,sn,create_time,type,type as type_desc,handling_fee,left_money,money_qr_code,apply_remark,audit_remark,transfer_voucher,transfer_remark,account,real_name,bank,subbank,apply_remark,pay_desc,transfer_time,transfer_voucher,transfer_remark,terminal';
        $info = WithdrawApply::field($field)
            ->append([ 'wechat_change_wait_receive' ])
            ->findOrEmpty($params['id'])
            ->toArray();
        
        $info['pay_desc'] = json_decode($info['pay_desc'], true) ? : new \stdClass();

        $config = WeChatConfigService::getWechatConfigByTerminal($info['terminal']);
        $info['pay_config'] = [
            'mch_id'    => $config['mch_id'] ?? '',
            'app_id'  => $config['app_id'] ?? '',
        ];
        
        return $info;
    }
    
    // 收款
    public static function receive($id, $user_id)
    {
        Db::startTrans();
        try {
            $withdraw = WithdrawApply::where(['id' => $id, 'user_id' => $user_id])->lock(true)->find();
            if (empty($withdraw['id'])){
                throw new \Exception('提现记录不存在');
            }
            if ($withdraw['status'] != WithdrawEnum::STATUS_ING || $withdraw['type'] != WithdrawEnum::TYPE_WECHAT_CHANGE){
                throw new \Exception('当前状态不能收款');
            }

            $transfer_way = ConfigService::get('config', 'transfer_way', 1);
            if ($transfer_way == WithdrawEnum::ENTERPRISE) {
                throw new \Exception('企业付款到零钱为自动到账，无需确认收款');
            }

            if ($transfer_way == WithdrawEnum::MERCHANT) {
                $detail = \app\adminapi\logic\withdraw\WechatMerchantTransferLogic::details($withdraw);
                if (!isset($detail['state'])) {
                    throw new \Exception('查询微信转账状态失败');
                }
                if ($detail['state'] == 'FAIL') {
                    throw new \Exception('微信转账已失败，无法确认收款');
                }
                if ($detail['state'] != 'SUCCESS') {
                    $retried = false;
                    for ($i = 0; $i < 3; $i++) {
                        sleep(1);
                        $detail = \app\adminapi\logic\withdraw\WechatMerchantTransferLogic::details($withdraw);
                        if (isset($detail['state']) && $detail['state'] == 'SUCCESS') {
                            $retried = true;
                            break;
                        }
                    }
                    if (!$retried) {
                        throw new \Exception('微信转账状态更新中，请稍后再试');
                    }
                }
            }

            $withdraw->status = WithdrawEnum::STATUS_SUCCESS;
            $withdraw->update_time = time();
            $withdraw->save();

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }
}