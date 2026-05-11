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

namespace app\shopapi\lists;


use app\common\enum\CouponEnum;
use app\common\lists\ListsExtendInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\Coupon;
use app\common\model\CouponList;
use app\common\model\Goods;
use app\common\model\GoodsCategory;

class CouponMyLists extends BaseShopDataLists implements ListsExtendInterface
{
    /**
     * @notes 我的优惠券搜索条件
     * @return array
     * @author 张无忌
     * @date 2021/7/29 18:14
     */
    public function setSearch(): array
    {
        return [];
    }

    /**
     * @notes 我的优惠券数量统计
     * @return mixed
     * @author 张无忌
     * @date 2021/7/29 18:14
     */
    public function extend()
    {
        $model = new CouponList();
        // 可使用的优惠券数量
        $detail['normal'] = $model->where([
            ['user_id', '=', $this->userId],
            ['status', '=', CouponEnum::USE_STATUS_NOT],
            ['invalid_time', '>=', time()]
        ])->count();
        // 已使用的优惠券数量
        $detail['use'] = $model->where([
            ['user_id', '=', $this->userId],
            ['status', '=', CouponEnum::USE_STATUS_OK]
        ])->count();
        // 已失效的优惠券数量
        $map1 = [
            ['user_id', '=', $this->userId],
            ['status', 'in', [CouponEnum::USE_STATUS_EXPIRE, CouponEnum::USE_STATUS_VOID]],
        ];
        $map2 = [
            ['user_id', '=', $this->userId],
            ['invalid_time', '<', time()]
        ];
        $detail['invalid'] = $model->whereOr([$map1, $map2])->count();

        return $detail;
    }

    /**
     * @notes 我的优惠券搜索条件
     * @return array
     * @author 张无忌
     * @date 2021/7/29 18:14
     */
    public function queryWhere(): array
    {
        $where = [];

        $status = isset($this->params['status']) ? $this->params['status'] : -1;

        switch ($status) {
            case CouponEnum::USE_STATUS_NOT:
                $where[] = [
                    ['CL.user_id', '=', $this->userId],
                    ['CL.status', '=', CouponEnum::USE_STATUS_NOT],
                    ['CL.invalid_time', '>=', time()]
                ];
                break;
            case CouponEnum::USE_STATUS_OK:
                $where[] = [
                    ['CL.user_id', '=', $this->userId],
                    ['CL.status', '=', CouponEnum::USE_STATUS_OK],
                ];
                break;
            case CouponEnum::USE_STATUS_EXPIRE:
            case CouponEnum::USE_STATUS_VOID:
                $where[] = [
                    ['CL.user_id', '=', $this->userId],
                    ['CL.status', 'in', [CouponEnum::USE_STATUS_EXPIRE, CouponEnum::USE_STATUS_VOID]],
                ];
                $where[] = [
                    ['CL.user_id', '=', $this->userId],
                    ['CL.invalid_time', '<', time()]
                ];
                break;
        }
        return $where;
    }

    /**
     * @notes 获取我的优惠券列表
     * @return array
     * @author 张无忌
     * @date 2021/7/29 18:16
     */
    public function lists(): array
    {
        $model = new Coupon();
        $lists = $model->alias('C')
            ->field([
                'C.id,CL.id as CL_id,C.name,C.money,C.condition_type','C.discount_max_money','C.discount_ratio',
                'C.condition_money,C.use_goods_type,C.use_goods_ids,C.use_goods_category_ids,C.use_time_type',
                'C.use_time_start,C.use_time_end,C.use_time,CL.status,CL.invalid_time'
            ])
            ->append([ 'condition', 'use_time_text', 'use_time_text2', 'use_type', 'tips' ])
            ->whereOr($this->queryWhere())
            ->join('coupon_list CL', 'C.id = CL.coupon_id')
            ->order('id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();
        
        foreach ($lists as &$item) {

            $item['use_scene'] = $item['use_type'];
            $item['effective_time'] = $item['use_time_text'];
            
            unset($item['use_goods_type']);
            unset($item['use_goods_ids']);
            unset($item['use_goods_category_ids']);
            unset($item['use_time_type']);
            unset($item['use_time_start']);
            unset($item['use_time_end']);
            unset($item['use_time']);
        }

        return $lists;
    }

    /**
     * @notes 我的优惠券数量
     * @return int
     * @author 张无忌
     * @date 2021/7/29 18:16
     */
    public function count(): int
    {
        $model = new Coupon();
        return $model->alias('C')
            ->field([
                'C.id,CL.id as CL_id,C.name,C.money,C.condition_type',
                'C.condition_money,C.use_goods_type,C.use_goods_ids,C.use_time_type',
                'C.use_time_start,C.use_time_end,C.use_time,CL.status'
            ])
            ->whereOr($this->queryWhere())
            ->join('coupon_list CL', 'C.id = CL.coupon_id')
            ->count();
    }
}