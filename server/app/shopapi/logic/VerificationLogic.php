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

namespace app\shopapi\logic;


use app\common\enum\AfterSaleEnum;
use app\common\enum\OrderEnum;
use app\common\enum\OrderLogEnum;
use app\common\enum\VerificationEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\AfterSale;
use app\common\model\Order;
use app\common\model\OrderGoods;
use app\common\model\OrderLog;
use app\common\model\SelffetchShop;
use app\common\model\SelffetchVerifier;
use app\common\model\Verification;
use app\common\service\ConfigService;

class VerificationLogic extends BaseLogic
{
    /**
     * @notes 提货核销
     * @param $params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2021/8/27 5:38 下午
     */
    public static function verification($params)
    {
        $result = Order::where('pickup_code',$params['pickup_code'])
            ->with(['order_goods' => function ($query) {
                $query->field('id,order_id,goods_snap,goods_name,goods_num')
                    ->append(['goods_image', 'spec_value_str'])
                    ->hidden(['goods_snap']);
            }])
            ->append(['verification_status_desc'])
            ->field('id,address,verification_status,selffetch_shop_id,belong_store_id')
            ->find()
            ->toArray();

        //校验是否有售后
        foreach ($result['order_goods'] as &$goods) {
            $goods['after_sale'] = AfterSale::where('order_goods_id', $goods['id'])
                ->field([ 'id', 'order_goods_id', 'status' ])
                ->order('id desc')
                ->findOrEmpty();
        }

        $result['contact'] = $result['address']->contact;
        unset($result['address']);

        // 门店信息: 核销门店(自提门店) 与 归属门店(下单用户所属加盟店)
        $shopNames = SelffetchShop::getNameMap([
            $result['selffetch_shop_id'] ?? 0,
            $result['belong_store_id'] ?? 0,
        ]);
        $result['selffetch_shop_name'] = $shopNames[intval($result['selffetch_shop_id'] ?? 0)] ?? '';
        $result['belong_store_name'] = $shopNames[intval($result['belong_store_id'] ?? 0)] ?? '';
        $result['is_cross_store'] = (intval($result['belong_store_id'] ?? 0) > 0
            && intval($result['belong_store_id']) != intval($result['selffetch_shop_id'])) ? 1 : 0;

        return $result;
    }

    /**
     * @notes 确认提货
     * @param $params
     * @return bool
     * @author ljj
     * @date 2021/8/27 6:05 下午
     */
    public static function verificationConfirm($params)
    {
        try {
            $order = Order::find($params['id']);
            if (empty($order)) {
                throw new \Exception('订单不存在');
            }
            // 幂等兜底:防止重复/并发提交写入多条核销记录与跨店结算明细
            if ($order['verification_status'] == OrderEnum::WRITTEN_OFF) {
                throw new \Exception('订单已核销');
            }
            $selffetch_verifier = SelffetchVerifier::where(['user_id'=>$params['user_id'],'selffetch_shop_id'=>$order['selffetch_shop_id'],'status'=>1])->find();
            if (empty($selffetch_verifier)) {
                throw new \Exception('非门店核销员，无法核销订单');
            }

            //添加核销记录
            $snapshot = [
                'sn' => $selffetch_verifier['sn'],
                'name' => $selffetch_verifier['name']
            ];
            // 核销门店 = 核销员所属自提门店(上文已校验该核销员必须绑定订单的自提门店)
            $handleStoreId = intval($order['selffetch_shop_id'] ?: 0);
            $verification = new Verification;
            $verification->order_id = $order['id'];
            $verification->selffetch_shop_id = $order['selffetch_shop_id'];
            $verification->belong_store_id = intval($order['belong_store_id'] ?? 0);
            $verification->handle_store_id = $handleStoreId;
            $verification->handle_id = $params['user_id'];
            $verification->verification_scene = VerificationEnum::TYPE_USER;
            $verification->snapshot = json_encode($snapshot);
            $verification->save();

            // 跨店结算: 自提 + 归属店 != 核销店 时生成结算明细
            \app\common\logic\StoreSettlementLogic::createForOrder($order, $handleStoreId);

            //更新订单状态
            $order->order_status = OrderEnum::STATUS_FINISH;
            $order->verification_status = OrderEnum::WRITTEN_OFF;
            $order->confirm_take_time = time();
            $order->after_sale_deadline = self::getAfterSaleDeadline();
            $order->save();

            //订单日志
            (new OrderLog())->record([
                'type' => OrderLogEnum::TYPE_USER,
                'channel' => OrderLogEnum::USER_VERIFICATION,
                'order_id' => $order['id'],
                'operator_id' => $params['user_id'],
            ]);

            return true;

        } catch (\Exception $e) {
            //错误
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 当前用户是否为门店核销员(用于核销入口显隐与页面门禁)
     * @param int $userId
     * @return array
     * @author likeshop
     * @date 2026/9/23
     */
    public static function isVerifier($userId)
    {
        $verifier = SelffetchVerifier::where(['user_id' => $userId, 'status' => 1])
            ->field('id,selffetch_shop_id')
            ->findOrEmpty()
            ->toArray();

        if (empty($verifier)) {
            return ['is_verifier' => 0, 'store_id' => 0, 'store_name' => ''];
        }

        $shopId = intval($verifier['selffetch_shop_id']);
        $shopNames = SelffetchShop::getNameMap([$shopId]);

        return [
            'is_verifier' => 1,
            'store_id' => $shopId,
            'store_name' => $shopNames[$shopId] ?? '',
        ];
    }

    /**
     * @notes 获取当前售后
     * @return float|int
     * @author ljj
     * @date 2021/9/1 3:09 下午
     */
    public static function getAfterSaleDeadline()
    {
        //是否关闭维权
        $afterSale = ConfigService::get('transaction', 'after_sales');
        //可维权时间
        $afterSaleDays = ConfigService::get('transaction', 'after_sales_days');

        if ($afterSale == YesNoEnum::NO) {
            $afterSaleDeadline = time();
        } else {
            $afterSaleDeadline = ($afterSaleDays * 24 * 60 * 60) + time();
        }

        return $afterSaleDeadline;
    }
}