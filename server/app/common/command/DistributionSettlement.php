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

namespace app\common\command;

use app\adminapi\logic\distribution\DistributionConfigLogic;
use app\adminapi\logic\distribution\DistributionLevelLogic;
use app\common\enum\AccountLogEnum;
use app\common\enum\AfterSaleEnum;
use app\common\enum\DistributionOrderGoodsEnum;
use app\common\enum\NoticeEnum;
use app\common\enum\OrderEnum;
use app\common\logic\AccountLogLogic;
use app\common\model\AfterSale;
use app\common\model\User;
use app\common\model\DistributionOrderGoods;
use app\common\model\OrderGoods;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;
use think\facade\Log;

/**
 * 分销结算命令
 * Class DistributionSettlement
 * @package app\command
 */
class DistributionSettlement extends Command
{
    protected function configure()
    {
        $this->setName('distribution_settlement')
            ->setDescription('结算分销订单');
    }

    protected function execute(Input $input, Output $output)
    {
        // 结算时间
        $config = DistributionConfigLogic::getConfig();
        $time = time() - $config['settlement_time'] * 24 * 60 * 60;

        Db::startTrans();
        try{
            // 正常收货后的情况
            $where1 = [
                ['dog.status', '=', DistributionOrderGoodsEnum::UN_RETURNED],
                ['o.order_status', '=', OrderEnum::STATUS_FINISH],
                // 确认收货时间 <= 当前时间 - 结算时间
                ['o.confirm_take_time', '<=', $time],
            ];
            // 未确认收货前，发生退款或取消订单的情况
            $where2 = [
                ['dog.status', '=', DistributionOrderGoodsEnum::UN_RETURNED],
                ['o.order_status', '=', OrderEnum::STATUS_CLOSE],
                ['o.confirm_take_time', 'exp', Db::raw('is null')],
            ];

            $field = 'dog.id,dog.user_id,dog.order_goods_id,dog.earnings,o.sn as order_sn';

            $lists = DistributionOrderGoods::alias('dog')
                ->leftJoin('order_goods og', 'og.id = dog.order_goods_id')
                ->leftJoin('order o', 'o.id = og.order_id')
                ->field($field)
                ->whereOr([$where1, $where2])
                ->select()
                ->toArray();

            foreach ($lists as $item) {
                // 判断当前分销订单是否允许结算
                if (!self::canSettle($item)) {
                    continue;
                }

                // 增加用户收益
                self::incUserEarning($item);

                // 记录账户流水
                AccountLogLogic::add($item['user_id'],AccountLogEnum::BW_INC_DISTRIBUTION_SETTLE,AccountLogEnum::INC,$item['earnings'], $item['order_sn'],'分销订单');

                // 更新分销订单状态
                DistributionOrderGoods::update([
                    'id' => $item['id'],
                    'status' => DistributionOrderGoodsEnum::RETURNED,
                    'settlement_time' => time()
                ]);

                // 更新分销商等级
                DistributionLevelLogic::updateDistributionLevel($item['user_id']);

                // 消息通知
                event('Notice', [
                    'scene_id' => NoticeEnum::EARNINGS_NOTICE,
                    'params' => [
                        'user_id' => $item['user_id'],
                        'earnings' => $item['earnings']
                    ]
                ]);
            }

            Db::commit();
        }catch(\Exception $e) {
            Db::rollback();
            Log::write('结算分销订单出错:'.$e->__toString());
        }
    }

    /**
     * @notes 判断分销订单是否需要结算
     * @param $item
     * @author Tab
     * @date 2021/7/28 11:40
     */
    public static function canSettle($item)
    {
//        $orderId = OrderGoods::where('id', $item['order_goods_id'])->value('order_id');
//        $afterSale = AfterSale::field('status')->where('order_id', $orderId)->select()->toArray();
        $afterSale = AfterSale::field('status')->where('order_goods_id', $item['order_goods_id'])->order('id', 'desc')->findOrEmpty()->toArray();
        if (empty($afterSale)) {
            // 到了结算时间，订单无售后，可结算
            return true;
        }

//        foreach($afterSale as $subItem) {
//            // 订单处于售后中,暂不可结算
//            if($subItem['status'] == AfterSaleEnum::STATUS_ING) {
//                return false;
//            }
//            // 订单已售后成功,分销单置为失效状态,不可结算
//            if($subItem['status'] == AfterSaleEnum::STATUS_SUCCESS) {
//                self::invalid($item);
//                return false;
//            }
//        }

        // 订单商品处于售后中,暂不可结算
        if($afterSale['status'] == AfterSaleEnum::STATUS_ING) {
            return false;
        }
        // 订单商品已售后成功,分销单置为失效状态,不可结算
        if($afterSale['status'] == AfterSaleEnum::STATUS_SUCCESS) {
            self::invalid($item);
            return false;
        }

        // 有售后但都是售后失败(即未产生退款)，可结算
        return true;
    }

    /**
     * @notes 设置分销订单为已失效状态
     * @param $item
     * @author Tab
     * @date 2021/8/5 11:04
     */
    public static function invalid($item)
    {
        $distributionOrderGoods = DistributionOrderGoods::findOrEmpty($item['id']);
        $distributionOrderGoods->status = DistributionOrderGoodsEnum::EXPIRED;
        $distributionOrderGoods->settlement_time = time();
        $distributionOrderGoods->save();
    }

    /**
     * @notes 增加用户收益
     * @param $item
     * @author Tab
     * @date 2021/8/5 11:36
     */
    public function incUserEarning($item)
    {
        $user = User::findOrEmpty($item['user_id']);
        
        if (! $user->isEmpty()) {
            $user->user_earnings = is_null($user->user_earnings) ? 0 : $user->user_earnings;
            $user->user_earnings = $user->user_earnings + $item['earnings'];
            $user->save();
        }
        
    }
}