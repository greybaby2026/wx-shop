<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\common\enum;


/**
 * 门店结算账单枚举
 * Class StoreSettlementEnum
 * @package app\common\enum
 */
class StoreSettlementEnum
{
    // 账单状态（ls_store_settlement.status）
    const STATUS_WAIT_CONFIRM = 0;//待确认
    const STATUS_CONFIRMED    = 1;//已确认
    const STATUS_PAID         = 2;//已付款

    // 账单周期类型（ls_store_settlement.period_type）
    const PERIOD_DAY   = 1;//日
    const PERIOD_WEEK  = 2;//周
    const PERIOD_MONTH = 3;//月

    // 结算单价来源（ls_store_settlement_order.cost_source）
    // ⚠️ 后台结算页对这三个值的展示文案是前端硬编码的：["协议价","成本价","待复核"]
    //    取值为 3 时显示红色 danger 标签。故取值必须与前端语义对齐，不可随意调整。
    const COST_SOURCE_RATIO    = 1;//按「成交价×比例」结算（前端显示"协议价"）
    const COST_SOURCE_SUPPLY   = 2;//按商品协商价结算（前端显示"成本价"）
    const COST_SOURCE_FAILED   = 3;//取价失败，金额为 0（前端显示红色"待复核"）

    /**
     * @notes 账单状态描述
     * @param bool|int $status true 返回全部；传值返回单项
     * @return array|string
     */
    public static function getStatusDesc($status = true)
    {
        $desc = [
            self::STATUS_WAIT_CONFIRM => '待确认',
            self::STATUS_CONFIRMED    => '已确认',
            self::STATUS_PAID         => '已付款',
        ];
        if ($status === true) {
            return $desc;
        }
        return $desc[$status] ?? '未知';
    }

    /**
     * @notes 账单周期类型描述
     * @param bool|int $period
     * @return array|string
     */
    public static function getPeriodDesc($period = true)
    {
        $desc = [
            self::PERIOD_DAY   => '日',
            self::PERIOD_WEEK  => '周',
            self::PERIOD_MONTH => '月',
        ];
        if ($period === true) {
            return $desc;
        }
        return $desc[$period] ?? '日';
    }

    /**
     * @notes 结算单价来源描述
     * @param bool|int $source
     * @return array|string
     */
    public static function getCostSourceDesc($source = true)
    {
        $desc = [
            self::COST_SOURCE_RATIO    => '按成交价×比例',
            self::COST_SOURCE_SUPPLY   => '按商品协商价',
            self::COST_SOURCE_FAILED   => '取价失败（待复核）',
        ];
        if ($source === true) {
            return $desc;
        }
        return $desc[$source] ?? '未知';
    }
}
