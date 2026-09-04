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

namespace app\adminapi\logic\distribution;

use app\common\enum\DistributionLevelEnum;
use app\common\enum\DistributionOrderGoodsEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\Distribution;
use app\common\model\DistributionGoods;
use app\common\model\DistributionLevel;
use app\common\model\DistributionLevelUpdate;
use app\common\model\DistributionOrderGoods;
use app\common\model\Order;
use think\facade\Db;

/**
 * 分销会员等级逻辑层
 * Class DistributionLevelLogic
 * @package app\adminapi\logic\distribution
 */
class DistributionLevelLogic extends BaseLogic
{
    /**
     * @notes 添加分销会员等级
     * @param $params
     * @author Tab
     * @date 2021/7/22 11:22
     */
    public static function add($params)
    {
        Db::startTrans();
        try{
            // 写入等级主表
            $params['remark'] = $params['remark'] ?? '';
            $newLevel = DistributionLevel::create($params);

            // 写入升级条件表
            self::addUpdateCondition($params['update_condition'], $newLevel->id);

            // 处理分销商品比例
            self::updateDistributionGoods($newLevel->id);

            Db::commit();
            return true;
        }catch(\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 写入升级条件表
     * @param $updateCondition
     * @param $level_id
     * @throws \Exception
     * @author Tab
     * @date 2021/7/22 14:28
     */
    public static function addUpdateCondition($updateCondition, $level_id)
    {
        $updateConditionData = [];
        foreach($updateCondition as $item) {
            // 判断是否在规定的条件字段
            $key = key($item);
            if(!in_array($key, DistributionLevelEnum::UPDATE_CONDITION_FIELDS, true)) {
                continue;
            }
            // 如果值为空跳过
            if (empty($item[$key])) {
                continue;
            }
            // 获取键对应值的字段名
            $valueField = DistributionLevelEnum::getValueFiled($key);
            $updateConditionData[] = [
                'level_id' => $level_id,
                'key' => key($item),
                $valueField => $item[$key]
            ];
        }
        (new DistributionLevelUpdate())->saveAll($updateConditionData);
    }

    /**
     * @notes 查看分销会员等级详情
     * @param $params
     * @return array
     * @author Tab
     * @date 2021/7/22 15:30
     */
    public static function detail($params)
    {
        $level = DistributionLevel::withoutField('create_time,update_time,delete_time')->findOrEmpty($params['id']);
        if($level->isEmpty()) {
            return [];
        }
        $level = $level->toArray();
        // 默认等级
        if($level['is_default']) {
           unset($level['third_ratio']);
           unset($level['is_default']);
           unset($level['update_relation']);
           return $level;
        }
        // 自定义等级
        $level['update_condition'] = self::getUpdateCondition($level);
        unset($level['third_ratio']);
        unset($level['is_default']);

        return $level;
    }

    /**
     * @notes 获取升级条件
     * @param $level
     * @return array
     * @author Tab
     * @date 2021/7/22 15:29
     */
    public static function getUpdateCondition($level)
    {
        $updateCondition = DistributionLevelUpdate::where('level_id', $level['id'])->column('key,value_int,value_decimal,value_text');
        $updateConditionData = [];
        foreach($updateCondition as $item) {
            if($item['value_int']) {
                $updateConditionData[$item['key']] = $item['value_int'];
                continue;
            }
            if($item['value_decimal']) {
                $updateConditionData[$item['key']] = $item['value_decimal'];
                continue;
            }
            if($item['value_text']) {
                $updateConditionData[$item['key']] = $item['value_text'];
                continue;
            }
        }
        return $updateConditionData;
    }

    /**
     * @notes 编辑分销会员等级
     * @param $params
     * @return bool
     * @author Tab
     * @date 2021/7/22 16:22
     */
    public static function edit($params)
    {
        Db::startTrans();
        try{
            $params['remark'] = $params['remark'] ?? '';
            $level = DistributionLevel::findOrEmpty($params['id']);
            if($level->isEmpty()) {
                throw new \Exception('等级不存在');
            }
            // 默认等级
            if($level->is_default) {
                $level->allowField(['name', 'self_ratio', 'first_ratio', 'second_ratio','remark'])->save($params);
                Db::commit();
                return true;
            }
            // 自定义等级 - 更新主表信息
            if(!$params['weights'] > 1) {
                throw new \Exception('级别须大于1');
            }
            if(!isset($params['update_relation'])) {
                throw new \Exception('请选择升级关系');
            }
            if(!isset($params['update_condition']) || !count($params['update_condition'])) {
                throw new \Exception('请选择升级条件');
            }
            $level->allowField(['name', 'weights', 'self_ratio', 'first_ratio', 'second_ratio','remark', 'update_relation'])->save($params);

            // 自定义等级 - 删除旧升级条件
            $deleteIds = DistributionLevelUpdate::where('level_id', $level->id)->column('id');
            DistributionLevelUpdate::destroy($deleteIds);

            // 自定义等级 - 添加新的升级条件
            self::addUpdateCondition($params['update_condition'], $level->id);

            Db::commit();
            return true;
        }catch(\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 删除分销会员等级
     * @param $params
     * @return false
     * @author Tab
     * @date 2021/7/22 16:49
     */
    public static function delete($params)
    {
        Db::startTrans();
        try{
            $level = DistributionLevel::findOrEmpty($params['id']);
            if($level->isEmpty()) {
                throw new \Exception('等级不存在');
            }
            if($level->is_default) {
                throw new \Exception('系统默认等级不允许删除');
            }

            // 重置该等级下的分销会员为系统默认等级
            $defaultId = DistributionLevel::where('is_default', DistributionLevelEnum::IS_DEFAULT_YES)->value('id');
            Distribution::where('level_id', $level->id)->update(['level_id' => $defaultId]);

            // 删除升级条件
            $deleteIds = DistributionLevelUpdate::where('level_id', $level->id)->column('id');
            DistributionLevelUpdate::destroy($deleteIds);

            // 删除该等级下的分销商品比例
            $deleteIds = DistributionGoods::where('level_id', $level->id)->column('id');
            DistributionGoods::destroy($deleteIds);

            // 删除等级
            $level->delete();

            Db::commit();
            return true;
        }catch(\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 更新分销商等级（按分销树深度）
     * depth=0（无上级）→ 大将军王(id=1, weights=1)
     * depth=1（有一级上级）→ 大将军(id=2, weights=2)
     * depth=2（有二级上级）→ 将军(id=5, weights=3)
     * @param $userId
     * @author Tab
     * @date 2021/7/26 19:14
     */
    public static function updateDistributionLevel($userId)
    {
        $distInfo = Distribution::where('user_id', $userId)->findOrEmpty();
        if ($distInfo->isEmpty() || !$distInfo->is_distribution) {
            return false;
        }

        $user = Db::name('user')->where('id', $userId)->field('first_leader,second_leader')->find();
        if (empty($user)) {
            return false;
        }

        $targetLevelId = self::getLevelIdByDepth($user['first_leader'], $user['second_leader']);

        if ($distInfo->level_id != $targetLevelId) {
            Distribution::where(['user_id' => $userId])->update(['level_id' => $targetLevelId]);
        }

        return true;
    }

    /**
     * @notes 根据分销树深度获取等级ID
     * @param int $firstLeader
     * @param int $secondLeader
     * @return int
     */
    public static function getLevelIdByDepth($firstLeader, $secondLeader)
    {
        if (empty($secondLeader)) {
            if (empty($firstLeader)) {
                $weights = 1;
            } else {
                $weights = 2;
            }
        } else {
            $weights = 3;
        }

        $level = DistributionLevel::where('weights', $weights)
            ->where('is_default', $weights == 1 ? YesNoEnum::YES : YesNoEnum::NO)
            ->whereNull('delete_time')
            ->findOrEmpty();

        if ($level->isEmpty()) {
            $level = DistributionLevel::where('weights', $weights)
                ->whereNull('delete_time')
                ->findOrEmpty();
        }

        if ($level->isEmpty()) {
            return DistributionLevel::where('is_default', YesNoEnum::YES)->value('id');
        }

        return $level->id;
    }

    /**
     * @notes 判断是否满足当前等级的升级条件
     * @param $userId
     * @param $level
     * @return bool
     * @author Tab
     * @date 2021/7/26 19:10
     */
    public static function isMeetConditions($userId, $level)
    {
        // 任一条件满足升级
        $updateRelation = $level['update_relation'];

        if($updateRelation == DistributionLevelEnum::UPDATE_RELATION_OR) {
            $flagOr = self::singleConsumptionAmountFlag($userId, $level, $updateRelation)
                || self::cumulativeConsumptionAmountFlag($userId, $level, $updateRelation)
                || self::cumulativeConsumptionTimesFlag($userId, $level, $updateRelation)
                || self::returnedCommissionFlag($userId, $level, $updateRelation);
            return $flagOr;
        }

        // 全部条件满足升级
        if($updateRelation == DistributionLevelEnum::UPDATE_RELATION_AND) {
            $flagAnd = self::singleConsumptionAmountFlag($userId, $level, $updateRelation)
                && self::cumulativeConsumptionAmountFlag($userId, $level, $updateRelation)
                && self::cumulativeConsumptionTimesFlag($userId, $level, $updateRelation)
                && self::returnedCommissionFlag($userId, $level, $updateRelation);
            return $flagAnd;
        }
    }

    /**
     * @notes 判断是否满足单笔消费金额条件
     * @param $userId
     * @param $level
     * @return bool
     * @author Tab
     * @date 2021/7/26 18:55
     */
    public static function singleConsumptionAmountFlag($userId, $level, $updateRelation)
    {
        $condition = DistributionLevelUpdate::field('value_int,value_decimal,value_text')
            ->where([
                'level_id' => $level['id'],
                'key' => 'singleConsumptionAmount'
            ])
            ->findOrEmpty();
        if($condition->isEmpty()) {
            // 等级条件为满足任一条件(updateRelation=1)  返回false (满足已设置的任一条件时才升级,未设置的条件归为未满足,返回false)
            // 等级条件为满足全部条件(updateRelation=2)  返回true  (满足已设置的所有条件时才升级,未设置的条件归为已满足,返回true)
            return $updateRelation == DistributionLevelEnum::UPDATE_RELATION_AND;
        }
        $singleConsumptionAmount = Order::where([
            'user_id' =>  $userId,
            'pay_status' => YesNoEnum::YES
        ])
        ->order('id', 'desc')
        ->findOrEmpty();
        if($singleConsumptionAmount->isEmpty()) {
            return false;
        }
        if($singleConsumptionAmount->order_amount >= $condition->value_decimal) {
            return true;
        }
        return false;
    }

    /**
     * @notes 判断是否满足累计消费金额条件
     * @param $userId
     * @param $level
     * @return bool
     * @author Tab
     * @date 2021/7/26 18:55
     */
    public static function cumulativeConsumptionAmountFlag($userId, $level, $updateRelation)
    {
        $condition = DistributionLevelUpdate::field('value_int,value_decimal,value_text')
            ->where([
                'level_id' => $level['id'],
                'key' => 'cumulativeConsumptionAmount'
            ])
            ->findOrEmpty();
        if($condition->isEmpty()) {
            // 等级条件为满足任一条件(updateRelation=1)  返回false (满足已设置的任一条件时才升级,未设置的条件归为未满足,返回false)
            // 等级条件为满足全部条件(updateRelation=2)  返回true  (满足已设置的所有条件时才升级,未设置的条件归为已满足,返回true)
            return $updateRelation == DistributionLevelEnum::UPDATE_RELATION_AND;
        }
        $cumulativeConsumptionAmount = Order::where([
            'user_id' =>  $userId,
            'pay_status' => YesNoEnum::YES
        ])->sum('order_amount');
        if($cumulativeConsumptionAmount >= $condition->value_decimal) {
            return true;
        }
        return false;
    }

    /**
     * @notes 判断是否满足累计消费次数条件
     * @param $userId
     * @param $level
     * @return bool
     * @author Tab
     * @date 2021/7/26 18:56
     */
    public static function cumulativeConsumptionTimesFlag($userId, $level, $updateRelation)
    {
        $condition = DistributionLevelUpdate::field('value_int,value_decimal,value_text')
            ->where([
                'level_id' => $level['id'],
                'key' => 'cumulativeConsumptionTimes'
            ])
            ->findOrEmpty();
        if($condition->isEmpty()) {
            // 等级条件为满足任一条件(updateRelation=1)  返回false (满足已设置的任一条件时才升级,未设置的条件归为未满足,返回false)
            // 等级条件为满足全部条件(updateRelation=2)  返回true  (满足已设置的所有条件时才升级,未设置的条件归为已满足,返回true)
            return $updateRelation == DistributionLevelEnum::UPDATE_RELATION_AND;
        }
        $cumulativeConsumptionTimes = Order::where([
            'user_id' =>  $userId,
            'pay_status' => YesNoEnum::YES
        ])->count();
        if($cumulativeConsumptionTimes >= $condition->value_int) {
            return true;
        }
        return false;
    }

    /**
     * @notes 判断是否消费已返佣金条件
     * @param $userId
     * @param $level
     * @return bool
     * @author Tab
     * @date 2021/7/26 18:56
     */
    public static function returnedCommissionFlag($userId, $level, $updateRelation)
    {
        $condition = DistributionLevelUpdate::field('value_int,value_decimal,value_text')
            ->where([
                'level_id' => $level['id'],
                'key' => 'returnedCommission'
            ])
            ->findOrEmpty();
        if($condition->isEmpty()) {
            // 等级条件为满足任一条件(updateRelation=1)  返回false (满足已设置的任一条件时才升级,未设置的条件归为未满足,返回false)
            // 等级条件为满足全部条件(updateRelation=2)  返回true  (满足已设置的所有条件时才升级,未设置的条件归为已满足,返回true)
            return $updateRelation == DistributionLevelEnum::UPDATE_RELATION_AND;
        }
        $returnedCommission = DistributionOrderGoods::where([
            'user_id' => $userId,
            'status' => DistributionOrderGoodsEnum::RETURNED
        ])->sum('earnings');
        if($returnedCommission >= $condition->value_decimal) {
            return true;
        }
        return false;
    }

    /**
     * @notes 更新分销商品比例
     * @param $levelId
     * @author Tab
     * @date 2021/9/11 16:04
     */
    public static function updateDistributionGoods($levelId)
    {
        // 处理单独设置比例的商品,新增分销会等级佣金比例初始化为0
        $field = [
            'goods_id',
            'item_id',
        ];
        $distribution = DistributionGoods::distinct(true)->field($field)->where('rule', 2)->select()->toArray();
        $addData = [];
        foreach($distribution as $item) {
            $temp = [
                'goods_id' => $item['goods_id'],
                'item_id' => $item['item_id'],
                'level_id' => $levelId,
                'first_ratio' => 0,
                'second_ratio' => 0,
                'rule' => 2,
            ];
            $addData[] = $temp;
        }
        (new  DistributionGoods())->saveAll($addData);
    }
}