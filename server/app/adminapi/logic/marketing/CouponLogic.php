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

namespace app\adminapi\logic\marketing;


use app\common\enum\CouponEnum;
use app\common\model\Coupon;
use app\common\model\CouponList;
use app\common\model\Goods;
use app\common\service\TimeService;
use app\adminapi\lists\marketing\CouponRecordLists;
use Exception;

class CouponLogic
{
    /**
     * @notes 优惠券详细
     * @param $params
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author 张无忌
     * @date 2021/7/20 16:26
     */
    public static function detail($params)
    {
        $detail = (new Coupon())->withoutField('create_time,update_time,delete_time')
            ->append([ 'goods', 'goods_category' ])->findOrEmpty(intval($params['id']))->toArray();

        $detail['use_time_start'] = $detail['use_time_start'] ? date('Y-m-d H:i:s', $detail['use_time_start']) : '';
        $detail['use_time_end'] = $detail['use_time_end'] ? date('Y-m-d H:i:s', $detail['use_time_end']) : '';

        return $detail;
    }

    /**
     * @notes 添加优惠券
     * @param $params
     * @author 张无忌
     * @date 2021/7/19 19:01
     */
    public static function add($params)
    {
        Coupon::create([
            'sn'              => create_code(8),
            "name"            => $params['name'],
            "money"           => $params['money'],
            // 使用条件
            "condition_type"  => $params['condition_type'],
            "condition_money" => $params['condition_money'] ?? 0,
            "discount_ratio"    => $params['discount_ratio'] ?? 0,
            'discount_max_money'=> $params['discount_max_money'] ?? 0,
            // 发放数量
            "send_total_type" => $params['send_total_type'],
            "send_total"      => $params['send_total'] ?? 0,
            // 使用时间
            "use_time_type"   => $params['use_time_type'],
            "use_time_start"  => empty($params['use_time_start']) ? 0 : strtotime($params['use_time_start']),
            "use_time_end"    => empty($params['use_time_end']) ? 0 : strtotime($params['use_time_end']),
            "use_time"        => $params['use_time'] ?? 0,
            // 获取方式
            "get_type"        => $params['get_type'],
            "get_num_type"    => $params['get_num_type'],
            "get_num"         => $params['get_num'] ?? 0,
            // 使用商品
            "use_goods_type"            => $params['use_goods_type'],
            "use_goods_ids"             => $params['use_goods_ids'] ?? [],
            "use_goods_category_ids"    => $params['use_goods_category_ids'] ?? [],
            
            "status"          => 1
        ]);
    }

    /**
     * @notes 编辑优惠券
     * @param $params
     * @return bool|string
     * @author 张无忌
     * @date 2021/7/20 15:14
     */
    public static function edit($params)
    {
        $coupon = (new Coupon())->findOrEmpty(intval($params['id']));

        if ($coupon['status'] == CouponEnum::COUPON_STATUS_NOT) {
            Coupon::update([
                "name"            => $params['name'],
                "money"           => $params['money'],
                // 使用条件
                "condition_type"  => $params['condition_type'],
                "condition_money" => $params['condition_money'] ?? 0,
                "discount_ratio"    => $params['discount_ratio'] ?? 0,
                'discount_max_money'=> $params['discount_max_money'] ?? 0,
                // 发放数量
                "send_total_type" => $params['send_total_type'],
                "send_total"      => $params['send_total'] ?? 0,
                // 使用时间
                "use_time_type"   => $params['use_time_type'],
                "use_time_start"  => empty($params['use_time_start']) ? 0 : strtotime($params['use_time_start']),
                "use_time_end"    => empty($params['use_time_end']) ? 0 : strtotime($params['use_time_end']),
                "use_time"        => $params['use_time'] ?? 0,
                // 获取方式
                "get_type"        => $params['get_type'],
                "get_num_type"    => $params['get_num_type'],
                "get_num"         => $params['get_num'] ?? 0,
                // 使用商品
                "use_goods_type"            => $params['use_goods_type'],
                "use_goods_ids"             => $params['use_goods_ids'] ?? [],
                "use_goods_category_ids"    => $params['use_goods_category_ids'] ?? [],
            ], ['id' => intval($params['id'])]);

        } elseif ($coupon['status'] == CouponEnum::COUPON_STATUS_CONDUCT) {

            if ($coupon['send_total_type'] == CouponEnum::SEND_TOTAL_TYPE_FIXED) {
                if ($coupon['send_total'] > $params['send_total']) {
                    return '调整后的发放数量不可少于原来的数量';
                }
            }

            Coupon::update([
                "name"            => $params['name'],
                "send_total_type" => $params['send_total_type'],
                "send_total"      => $params['send_total'] ?? 0,
            ], ['id' => intval($params['id'])]);

        } elseif ($coupon['status'] == CouponEnum::COUPON_STATUS_END) {
            return '优惠券已结束,禁止编辑';
        }

        return true;
    }

    /**
     * @notes 删除优惠券
     * @param $params
     * @return bool|string
     * @author 张无忌
     * @date 2021/7/20 16:10
     */
    public static function delete($params)
    {
        $couponList = (new CouponList())
            ->where(['coupon_id'=>intval($params['id'])])
            ->findOrEmpty()->toArray();

        if ($couponList) {
            return '优惠券已被用户领取,不可删除';
        }

        Coupon::destroy(intval($params['id']));
        return true;
    }

    /**
     * @notes 优惠券基本信息
     * @param $params
     * @return array
     * @author 张无忌
     * @date 2021/7/21 10:23
     */
    public static function info($params)
    {
        $model = new Coupon();
        $coupon = $model
            ->withoutField('update_time,delete_time')
            ->append([ 'use_time_text', 'discount_content', 'status_text', 'use_number', 'receive_number', 'surplus_number', 'send_total_text' ])
            ->findOrEmpty($params['id'])
            ->toArray();

        $coupon['get_method'] = CouponEnum::getTypeDesc($coupon['get_type']);


        unset($coupon['condition_type']);
        unset($coupon['condition_money']);
        unset($coupon['send_total_type']);
        unset($coupon['use_time_type']);
        unset($coupon['use_time_start']);
        unset($coupon['use_time_end']);
        unset($coupon['use_time']);
        unset($coupon['get_num_type']);
        unset($coupon['get_num']);
        unset($coupon['use_goods_type']);
        unset($coupon['use_goods_ids']);

        return $coupon;
    }

    /**
     * @notes 开启领取
     * @param $params
     * @author 张无忌
     * @date 2021/7/21 10:25
     */
    public static function open($params)
    {
        Coupon::update([
            'status' => CouponEnum::COUPON_STATUS_CONDUCT
        ], ['id'=>$params['id']]);
    }

    /**
     * @notes 结束领券
     * @param $params
     * @author 张无忌
     * @date 2021/7/21 10:26
     */
    public static function stop($params)
    {
        Coupon::update([
            'status' => CouponEnum::COUPON_STATUS_END
        ], ['id'=>$params['id']]);
    }

    /**
     * @notes 发放优惠券
     * @param $params
     * @return string
     * @author 张无忌
     * @date 2021/7/21 10:42
     */
    public static function send($params)
    {
        try {
            // 获取优惠券库存信息
            $coupon = (new Coupon())->where(['id'=>$params['id']])->append(['surplus_number'])->findOrEmpty()->toArray();

            if ($coupon['status'] == CouponEnum::COUPON_STATUS_NOT) {
                return '优惠券活动尚未开始,不能发放';
            }

            if ($coupon['status'] == CouponEnum::COUPON_STATUS_END) {
                return '优惠券活动已结束,不能发放';
            }

            $totalSendNum = $params['send_user_num'] * count($params['send_user']);
            if ($coupon['send_total_type'] == CouponEnum::SEND_TOTAL_TYPE_FIXED) {
                if ($totalSendNum > $coupon['surplus_number']) {
                    return '发放的总数量,超出库存数量,不能发放';
                } else {
//                    Coupon::update([
//                        'send_total' => ['dec', $totalSendNum]
//                    ], ['id' => $coupon['id']]);
                }
            }

            // 计算出券最后可用时间
            $invalid_time = 0;
            switch ($coupon['use_time_type']) {
                case CouponEnum::USE_TIME_TYPE_FIXED:
                    $invalid_time = $coupon['use_time_end'];
                    break;
                case CouponEnum::USE_TIME_TYPE_TODAY:
                    $invalid_time = time() + ($coupon['use_time'] * 86400);
                    break;
                case CouponEnum::USE_TIME_TYPE_TOMORROW:
                    $time = strtotime(date('Y-m-d', strtotime("+1 day")));
                    $invalid_time = $time + ($coupon['use_time'] * 86400);
            }


            // 指定用户发放
            $couponListModel = new CouponList();
            foreach ($params['send_user'] as $user_id) {
                $list = [];
                for ($i=1; $i<=$params['send_user_num']; $i++) {
                    $list[] = [
                        'channel'      => 1,
                        'coupon_code'  => create_code(),
                        'user_id'      => $user_id,
                        'coupon_id'    => $coupon['id'],
                        'order_id'     => 0,
                        'status'       => 0,
                        'use_time'     => 0,
                        'invalid_time' => $invalid_time
                    ];
                }
                $couponListModel->saveAll($list);
            }

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @notes 作废优惠券
     * @param $params
     * @author 张无忌
     * @date 2021/7/21 17:21
     */
    public static function void($params)
    {
        (new CouponList())
            ->whereIn('id', $params['cl_id'])
            ->update([
                'status' => CouponEnum::USE_STATUS_VOID,
                'update_time' => time()
            ]);
    }
}