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


use app\common\enum\ActivityEnum;
use app\common\enum\AfterSaleLogEnum;
use app\common\enum\OrderEnum;
use app\common\enum\OrderLogEnum;
use app\common\enum\PayEnum;
use app\common\enum\TeamEnum;
use app\common\model\GoodsActivity;
use app\common\model\Order;
use app\common\model\OrderLog;
use app\common\model\TeamActivity;
use app\common\model\TeamFound;
use app\common\model\TeamJoin;
use app\common\service\after_sale\AfterSaleService;
use Exception;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;
use think\facade\Log;

class TeamEnd extends Command
{
    protected function configure()
    {
        $this->setName('team_end')
            ->setDescription('结束已超时的拼团');
    }

    /**
     * @notes 处理失败的团
     * @param Input $input
     * @param Output $output
     * @return bool|int|null
     * @throws Exception
     * @author 张无忌
     * @date 2021/8/4 15:43
     */
    protected function execute(Input $input, Output $output)
    {
        Db::startTrans();
        try {
            $time = time();

            // 1、获取并关闭已结束的活动
            $team_ids = (new TeamActivity())->where(['status' => TeamEnum::TEAM_STATUS_CONDUCT])->where([['end_time', '<=', $time]])->column('id');
            (new TeamActivity())->whereIn('id', $team_ids)->update(['status' => TeamEnum::TEAM_STATUS_END, 'update_time' => $time]);

            // 2、找出拼团中&&拼团有效期结束的拼团记录
            $map1 = array(['invalid_time', '<=', $time], ['status', '=', 0]);
            $map2 = array(['team_id', 'in', $team_ids], ['status', '=', 0]);
            $teamFound1 = (new TeamFound())->withoutField('goods_snap')
                ->whereOr([$map1, $map2])
                ->select()->toArray();
            $teamFound2 = (new TeamFound())->alias('TF')
                ->withoutField('goods_snap')
                ->where('TF.people', 'exp', ' <= TF.join')
                ->where([['TF.invalid_time', '>', $time]])
                ->where('status', '=', 0)
                ->select()->toArray();
            $teamFounds = array_merge($teamFound1, $teamFound2);

            // 3、处理拼团的状态
            foreach ($teamFounds as $teamFound) {
                $teamActivity = (new TeamActivity())->findOrEmpty($teamFound['team_id'])->toArray();
                $is_automatic = $teamActivity['is_automatic'];

                // 获取参团记录数据
                $teamJoin = (new TeamJoin())->alias('TJ')
                    ->field('TJ.*,O.order_status,O.pay_status')
                    ->join('order O', 'O.id = TJ.order_id')
                    ->where(['TJ.found_id' => $teamFound['id']])
                    ->select()->toArray();

                // 获取该团的已支付数量
                $payTeamCount = 0;
                foreach ($teamJoin as $item) {
                    if ($item['order_status'] == 1 and $item['pay_status'] == 1) {
                        $payTeamCount += 1;
                    }
                }

                // 如果已满员,但是存在未支付订单,团时间结束时间未到,则不处理
                if (($teamFound['people'] == $teamFound['join'] && $teamFound['people'] > $payTeamCount)
                    and $teamFound['invalid_time'] >= $time) {
                    continue;
                }

                // 判断此团的订单状态: 如果存在未支付订单则直接标记失败
                if (in_array(0, array_column($teamJoin, 'pay_status'))) {
                    $this->teamFail($teamJoin, $teamFound, $time);
                } else {
                    // 自动成团/拼团成功
                    if ($is_automatic or ($teamFound['people'] == $teamFound['join'] && $teamFound['people'] == $payTeamCount)) {
                        $this->teamSuccess($teamJoin, $teamFound, $time);
                    } else {
                        $this->teamFail($teamJoin, $teamFound, $time);
                    }
                }
            }

            // 删除商品活动信息表的数据
            $goodsActivityIds = GoodsActivity::where([
                ['activity_type', '=', ActivityEnum::TEAM],
                ['activity_id', 'in', $team_ids],
            ])->column('id');
            if (count($goodsActivityIds)) {
                GoodsActivity::destroy($goodsActivityIds);
            }

            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            Log::write('结束已超时的拼团失败,失败原因:' . $e->getMessage());
            return false;
        }
    }

    /**
     * @notes 拼团成功
     * @param $teamJoin
     * @param $teamFound
     * @param $time
     * @author 张无忌
     * @date 2021/8/4 16:47
     */
    public function teamSuccess($teamJoin, $teamFound, $time)
    {
        // 把团标记成功
        TeamFound::update([
            'status'        => TeamEnum::TEAM_FOUND_SUCCESS,
            'team_end_time' => $time,
        ], ['id'=>$teamFound['id']]);

        // 把团记录以及订单标记成功
        foreach ($teamJoin as $item) {
            // 标记拼团状态为成功
            TeamJoin::update([
                'status' => TeamEnum::TEAM_FOUND_SUCCESS,
                'update_time' => $time,
                'team_end_time' => time()
            ], ['id' => $item['id']]);

            // 标记拼团订单状态成功
            Order::update([
                'is_team_success' => 1,
                'update_time' => $time
            ], ['id' => $item['order_id']]);
        }
    }

    /**
     * @notes 拼团失败,给已支付的订单退款
     * @param $teamJoin
     * @param $teamFound
     * @param $time
     * @throws @\think\Exception
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author 张无忌
     * @date 2021/8/4 15:27
     */
    public function teamFail($teamJoin, $teamFound, $time)
    {
        // 把团标记失败
        TeamFound::update([
            'status'        => TeamEnum::TEAM_FOUND_FAIL,
            'team_end_time' => $time,
        ], ['id'=>$teamFound['id']]);

        // 把团记录以及订单标记失败,并进行退款
        foreach ($teamJoin as $item) {
            // 1、拼团记录标记为失败
            TeamJoin::update([
                'status'      => TeamEnum::TEAM_FOUND_FAIL,
                'team_end_time' => $time,
                'update_time' => time()
            ], ['id'=>$item['id']]);

            // 2、订单拼团状态标记失败
            Order::update([
                'is_team_success' => 2,
                'update_time'     => time()
            ], ['id'=>$item['order_id']]);

            // 获取团的订单
            $teamOrder = (new Order())->where(['id'=>$item['order_id']])->findOrEmpty()->toArray();

            // 处于已支付状态的发起整单售后
            if ($teamOrder['pay_status'] == PayEnum::ISPAID) {
                AfterSaleService::orderRefund([
                    'order_id' => $teamOrder['id'],
                    'scene'    => AfterSaleLogEnum::BUYER_CANCEL_ORDER
                ]);
            }

            //更新订单为已关闭
            Order::update([
                'is_team_success' => 2,
                'order_status' => OrderEnum::STATUS_CLOSE,
                'cancel_time'  => time()
            ], ['id' => $teamOrder['id']]);

            // 订单日志
            (new OrderLog())->record([
                'type'        => OrderLogEnum::TYPE_USER,
                'channel'     => OrderLogEnum::USER_CANCEL_ORDER,
                'order_id'    => $teamOrder['id'],
                'operator_id' => $teamOrder['user_id'],
            ]);
        }
    }
}