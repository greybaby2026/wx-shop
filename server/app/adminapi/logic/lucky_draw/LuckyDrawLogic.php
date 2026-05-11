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

namespace app\adminapi\logic\lucky_draw;

use app\common\enum\LuckyDrawEnum;
use app\common\logic\BaseLogic;
use app\common\model\LuckyDraw;
use app\common\model\LuckyDrawPrize;
use think\facade\Db;

/**
 * 幸运抽奖
 */
class LuckyDrawLogic extends BaseLogic
{
    /**
     * @notes 获取奖品类型
     * @author Tab
     * @date 2021/11/24 9:50
     */
    public static function getPrizeType()
    {
        $data = [];
        $prizeTypes = LuckyDrawEnum::getPrizeTypeDesc();
        foreach ($prizeTypes as $key => $value) {
            $temp['value'] = $key;
            $temp['label'] = $value;
            $data[] = $temp;
        }
        return $data;
    }

    /**
     * @notes 添加幸运抽奖活动
     * @param $params
     * @author Tab
     * @date 2021/11/24 11:06
     */
    public static function add($params)
    {
        Db::startTrans();
        try {
            $activity = LuckyDraw::create([
                'sn' => generate_sn((new LuckyDraw()), 'sn'),
                'name' => $params['name'],
                'start_time' => strtotime($params['start_time']),
                'end_time' => strtotime($params['end_time']),
                'remark' => $params['remark'] ?? '',
                'probability' => $params['probability'],
                'auth_type' => $params['auth_type'],
                'user_level' => $params['user_level'] ? json_encode($params['user_level']) : '',
                'need_integral' => $params['need_integral'],
                'frequency_type' => $params['frequency_type'],
                'frequency' => $params['frequency'] ?? null,
                'describe' => $params['describe'],
                'show_winning_list' => $params['show_winning_list'],
                'top_image' => $params['top_image'],
                'start_button_image' => $params['start_button_image'],
                'prize_base_image' => $params['prize_base_image'],
                'container_image' => $params['container_image'],
                'background_image' => $params['background_image'],
                'share_image' => $params['share_image'] ?? '',
                'share_describe' => $params['share_describe'] ?? '',
            ]);

            // 添加奖品
            foreach ($params['prizes'] as $key => $prize) {
                LuckyDrawPrize::create([
                    'activity_id' => $activity->id,
                    'name' => $prize['name'],
                    'image' => $prize['image'],
                    'type' => $prize['type'],
                    'type_value' => $prize['type_value'],
                    'num' => $prize['num'],
                    'location' => $key + 1,
                ]);
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 查看幸运抽奖活动详情
     * @param $params
     * @author Tab
     * @date 2021/11/24 14:06
     */
    public static function detail($params)
    {
        $activity = LuckyDraw::withoutField('create_time,update_time,delete_time')
            ->append(['status_desc', 'start_time_desc', 'end_time_desc'])
            ->json(['user_level'],true)
            ->findOrEmpty($params['id'])->toArray();
        if (!empty($activity['user_level'])) {
            foreach ($activity['user_level'] as &$item) {
                $item = (int)($item);
            }
        }

        $activity['prizes'] = LuckyDrawPrize::withoutField('create_time,update_time,delete_time')
            ->append(['type_desc'])
            ->where('activity_id', $activity['id'])->select()->toArray();

        return $activity;
    }

    /**
     * @notes 编辑幸运抽奖活动
     * @param $params
     * @author Tab
     * @date 2021/11/24 14:23
     */
    public static function edit($params)
    {
        Db::startTrans();
        try {
            $activity = LuckyDraw::findOrEmpty($params['id']);
            switch ($activity->status) {
                case LuckyDrawEnum::WAIT:
                    LuckyDraw::update([
                        'name' => $params['name'],
                        'start_time' => strtotime($params['start_time']),
                        'end_time' => strtotime($params['end_time']),
                        'remark' => $params['remark'] ?? '',
                        'probability' => $params['probability'],
                        'auth_type' => $params['auth_type'],
                        'user_level' => $params['user_level'] ? json_encode($params['user_level']) : '',
                        'need_integral' => $params['need_integral'],
                        'frequency_type' => $params['frequency_type'],
                        'frequency' => $params['frequency'] ?? null,
                        'describe' => $params['describe'],
                        'show_winning_list' => $params['show_winning_list'],
                        'top_image' => $params['top_image'],
                        'start_button_image' => $params['start_button_image'],
                        'prize_base_image' => $params['prize_base_image'],
                        'container_image' => $params['container_image'],
                        'background_image' => $params['background_image'],
                        'share_image' => $params['share_image'] ?? '',
                        'share_describe' => $params['share_describe'] ?? '',
                    ],['id'=>$params['id']]);

                    //删除旧奖品
                    LuckyDrawPrize::where(['activity_id'=>$params['id']])->delete();
                    //添加新奖品
                    foreach ($params['prizes'] as $key => $prize) {
                        LuckyDrawPrize::create([
                            'activity_id' => $activity->id,
                            'name' => $prize['name'],
                            'image' => $prize['image'],
                            'type' => $prize['type'],
                            'type_value' => $prize['type_value'],
                            'num' => $prize['num'],
                            'location' => $key + 1,
                        ]);
                    }

                    break;
                case LuckyDrawEnum::ING:
                    // 进行中的只允许更新活动名称及时间
                    $activity->name = $params['name'];
                    $activity->start_time = strtotime($params['start_time']);
                    $activity->end_time = strtotime($params['end_time']);
                    $activity->save();
                    break;
                case LuckyDrawEnum::END:
                    throw new \Exception('已结束的活动不允许编辑');
            }

            Db::commit();
            return true;
        } catch(\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 开始幸运抽奖活动
     * @param $params
     * @author Tab
     * @date 2021/11/24 14:59
     */
    public static function start($params)
    {
        try {
            $activity = LuckyDraw::findOrEmpty($params['id']);
            if ($activity->status != LuckyDrawEnum::WAIT) {
                throw new \Exception('只有未开始的活动才能进行开始操作');
            }
            $activity->status = LuckyDrawEnum::ING;
            $activity->save();
            return true;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }


    /**
     * @notes 结束活动
     * @param $params
     * @return bool
     * @author Tab
     * @date 2021/11/24 15:05
     */
    public static function end($params)
    {
        try {
            $activity = LuckyDraw::findOrEmpty($params['id']);
            if ($activity->status != LuckyDrawEnum::ING) {
                throw new \Exception('只有进行中的活动才能结束');
            }
            $activity->status = LuckyDrawEnum::END;
            $activity->remark = $activity->remark . ' 后台结束活动';
            $activity->save();
            return true;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 删除幸运抽奖活动
     * @param $params
     * @return bool
     * @author Tab
     * @date 2021/11/24 15:15
     */
    public static function delete($params)
    {
        try {
            $activity = LuckyDraw::findOrEmpty($params['id']);
            if ($activity->status == LuckyDrawEnum::ING) {
                throw new \Exception('进行中的活动不能删除');
            }

            LuckyDraw::destroy($params['id']);

            return true;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }
}