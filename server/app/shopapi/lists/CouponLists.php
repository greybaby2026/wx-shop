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
use app\common\lists\ListsSearchInterface;
use app\common\model\Coupon;
use app\common\model\CouponList;
use app\common\service\TimeService;

class CouponLists extends BaseShopDataLists implements ListsSearchInterface
{
    /**
     * @notes 优惠券列表搜索条件
     * @return array
     * @author 张无忌
     * @date 2021/7/29 18:13
     */
    public function setSearch(): array
    {
        return [];
    }

    /**
     * @notes 获取优惠券列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author 张无忌
     * @date 2021/7/29 18:13
     */
    public function lists(): array
    {
        $model = new Coupon();
        
        $hidden = [
            'send_total_type', 'send_total',
            'use_time_type', 'use_time_start', 'use_time_end', 'use_time',
            'get_type', 'get_num_type', 'get_num',
            'use_goods_ids',
            'create_time', 'update_time', 'delete_time',
        ];
        
        $lists = $model->field(true)
            ->where(['status' => CouponEnum::COUPON_STATUS_CONDUCT])
            ->where(['get_type' => CouponEnum::GET_TYPE_USER])
            ->append([ 'is_available', 'is_receive', 'condition', 'is_empty', 'use_time_text', 'use_time_text2' ])
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
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            
            $item['use_scene'] = CouponEnum::getUseGoodsTypeDesc($item['use_goods_type']);
            
            $item['effective_time'] = $item['use_time_text'];
        }

        return $lists;
    }

    /**
     * @notes 获取优惠券数量
     * @return int
     * @author 张无忌
     * @date 2021/7/29 18:14
     */
    public function count(): int
    {
        $model = new Coupon();
        return $model->field(true)
            ->where(['status' => CouponEnum::COUPON_STATUS_CONDUCT])
            ->count();
    }
}