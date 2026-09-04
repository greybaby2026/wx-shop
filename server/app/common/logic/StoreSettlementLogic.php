<?php
namespace app\common\logic;

use app\common\enum\DeliveryEnum;
use app\common\model\GoodsItem;
use app\common\model\OrderGoods;
use app\common\model\StoreSettlement;
use app\common\model\StoreSettlementOrder;
use think\facade\Log;

/**
 * 门店跨店结算逻辑
 * 规则: 仅自提订单; 归属门店(付款方) != 核销门店(收款方) 才生成明细;
 * 金额按协议供货价, 缺失回退成本价, 再缺失为0并标记人工复核; 快递订单不参与
 */
class StoreSettlementLogic
{
    /**
     * 核销成功后调用: 按需生成跨店结算明细(失败不影响核销主流程)
     */
    public static function createForOrder($order, $handleStoreId = 0)
    {
        try {
            if (!$order || $order['delivery_type'] != DeliveryEnum::SELF_DELIVERY) {
                return true;
            }
            $payStoreId = intval($order['belong_store_id'] ?? 0);
            $receiveStoreId = intval($handleStoreId ?: $order['selffetch_shop_id']);
            if ($payStoreId <= 0 || $receiveStoreId <= 0 || $payStoreId == $receiveStoreId) {
                return true;
            }
            $exists = StoreSettlementOrder::where('order_id', $order['id'])->findOrEmpty();
            if (!$exists->isEmpty()) {
                return true;
            }
            $orderGoods = OrderGoods::where('order_id', $order['id'])->select()->toArray();
            $amount = 0;
            $source = 1;
            $detail = [];
            foreach ($orderGoods as $goods) {
                $snapRaw = $goods['goods_snap'];
                if (is_object($snapRaw)) {
                    $snap = json_decode(json_encode($snapRaw), true) ?: [];
                } elseif (is_array($snapRaw)) {
                    $snap = $snapRaw;
                } else {
                    $snap = json_decode((string) $snapRaw, true) ?: [];
                }
                $supply = floatval($snap['supply_price'] ?? 0);
                if ($supply <= 0) {
                    $item = GoodsItem::where('id', $goods['item_id'])->findOrEmpty();
                    $supply = floatval($item->isEmpty() ? 0 : $item['supply_price']);
                }
                if ($supply <= 0) {
                    $item = GoodsItem::where('id', $goods['item_id'])->findOrEmpty();
                    $supply = floatval($item->isEmpty() ? 0 : $item['cost_price']);
                    if ($supply > 0) {
                        $source = max($source, 2);
                    }
                }
                if ($supply <= 0) {
                    $source = 3;
                }
                $lineAmount = round($supply * intval($goods['goods_num']), 2);
                $amount += $lineAmount;
                $detail[] = [
                    'goods_name' => $goods['goods_name'],
                    'goods_num' => $goods['goods_num'],
                    'supply_price' => $supply,
                    'amount' => $lineAmount,
                ];
            }
            StoreSettlementOrder::create([
                'order_id' => $order['id'],
                'order_sn' => $order['sn'],
                'pay_store_id' => $payStoreId,
                'receive_store_id' => $receiveStoreId,
                'amount' => round($amount, 2),
                'cost_source' => $source,
                'detail' => json_encode($detail, JSON_UNESCAPED_UNICODE),
                'status' => 0,
                'create_time' => time(),
            ]);
            return true;
        } catch (\Throwable $e) {
            Log::write('store_settlement_error: ' . $e->getMessage());
            return true;
        }
    }

    /**
     * 结算中心: 按周期生成汇总账单 period_type 1日2周3月
     */
    public static function generateBill($periodType, $date, $adminId)
    {
        try {
            $ts = strtotime($date);
            if (!$ts) {
                return ['msg' => '日期格式不正确'];
            }
            switch (intval($periodType)) {
                case 2:
                    $start = strtotime(date('Y-m-d', strtotime('monday this week', $ts)));
                    $end = strtotime(date('Y-m-d', strtotime('sunday this week', $ts)));
                    break;
                case 3:
                    $start = strtotime(date('Y-m-01', $ts));
                    $end = strtotime(date('Y-m-t', $ts));
                    break;
                default:
                    $start = strtotime(date('Y-m-d', $ts));
                    $end = $start;
            }
            $startDate = date('Y-m-d', $start);
            $endDate = date('Y-m-d', $end);
            $startTs = $start;
            $endTs = $end + 86399;
            // 周期内未入账单的明细
            $details = StoreSettlementOrder::where([
                ['status', '=', 0],
                ['settlement_id', '=', 0],
                ['create_time', '>=', $startTs],
                ['create_time', '<=', $endTs],
            ])->select()->toArray();
            if (empty($details)) {
                return ['msg' => '该周期内无可结算明细'];
            }
            $groups = [];
            foreach ($details as $d) {
                $key = $d['pay_store_id'] . '_' . $d['receive_store_id'];
                $groups[$key][] = $d;
            }
            $created = [];
            foreach ($groups as $key => $rows) {
                list($payStoreId, $receiveStoreId) = explode('_', $key);
                // 防重: 同周期同门店对已存在账单
                $dup = StoreSettlement::where([
                    ['period_type', '=', intval($periodType)],
                    ['start_date', '=', $startDate],
                    ['end_date', '=', $endDate],
                    ['pay_store_id', '=', $payStoreId],
                    ['receive_store_id', '=', $receiveStoreId],
                ])->findOrEmpty();
                if (!$dup->isEmpty()) {
                    continue;
                }
                $amount = 0;
                $bill = StoreSettlement::create([
                    'sn' => generate_sn((new StoreSettlement()), 'sn'),
                    'period_type' => intval($periodType),
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'pay_store_id' => intval($payStoreId),
                    'receive_store_id' => intval($receiveStoreId),
                    'amount' => 0,
                    'order_count' => count($rows),
                    'status' => 0,
                    'create_admin_id' => intval($adminId),
                    'create_time' => time(),
                ]);
                foreach ($rows as $r) {
                    $amount += floatval($r['amount']);
                    StoreSettlementOrder::update([
                        'id' => $r['id'],
                        'settlement_id' => $bill->id,
                        'status' => 1,
                    ]);
                }
                StoreSettlement::update([
                    'id' => $bill->id,
                    'amount' => round($amount, 2),
                ]);
                $created[] = $bill->id;
            }
            if (empty($created)) {
                return ['msg' => '该周期账单已存在'];
            }
            return ['created' => $created, 'count' => count($created)];
        } catch (\Exception $e) {
            return ['msg' => $e->getMessage()];
        }
    }
}
