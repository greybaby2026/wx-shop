<?php
namespace app\common\logic;

use app\common\enum\DeliveryEnum;
use app\common\model\GoodsItem;
use app\common\model\OrderGoods;
use app\common\model\SelffetchShop;
use app\common\model\StoreSettlement;
use app\common\model\StoreSettlementOrder;
use app\common\service\ConfigService;
use think\facade\Db;
use think\facade\Log;

/**
 * 门店跨店结算逻辑
 * 参与范围: 仅自提订单; 归属门店(付款方) != 核销门店(收款方) 才生成明细; 快递订单不参与
 *
 * 结算基数: 以「小程序实际成交价」为准
 *   单件成交价 = 该行实付商品金额(扣除优惠、剔除运费) / 数量
 *   行结算额   = 单件结算单价 × 数量
 *
 * 结算比例(优先级): 归属门店(付款方)的 settlement_ratio > 全局配置 ratio
 * 结算开关: ls_config type=store_settlement
 *   mode = 1 按「成交价 × 比例」(默认)
 *   mode = 0 兼容旧口径, 按商品协商价(goods_item.supply_price → cost_price)
 *
 * 异常兜底: 比例未配置 / 成交价为 0 / 任一行取价失败 → 该行金额为 0, 且整单
 *   cost_source = 3; 后台结算明细会显示红色「待复核」标签, 需人工介入, 不会静默出错。
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
            //结算口径: 开关 mode(1按成交价×比例 / 0按商品协商价)
            $mode = intval(ConfigService::get('store_settlement', 'mode', 1));
            //比例: 归属门店(付款方)独立配置优先, 为0则用全局
            $globalRatio = floatval(ConfigService::get('store_settlement', 'ratio', 0));
            $shopRatio   = floatval(SelffetchShop::where('id', $payStoreId)->value('settlement_ratio') ?: 0);
            $ratio       = $shopRatio > 0 ? $shopRatio : $globalRatio;

            $orderGoods = OrderGoods::where('order_id', $order['id'])->select()->toArray();
            $amount    = 0;
            $baseTotal = 0;
            $source    = $mode == 1 ? 1 : 2; //1=按比例 2=按商品协商价; 任一行取价失败则升级为3
            $detail    = [];
            foreach ($orderGoods as $goods) {
                $num = intval($goods['goods_num']);
                //单件成交价 = 该行实付商品金额(total_pay_price 已扣优惠) 剔除运费 后按数量摊分
                $rowPaid   = floatval($goods['total_pay_price']) - floatval($goods['express_price']);
                $basePrice = $num > 0 ? round($rowPaid / $num, 2) : 0;

                if ($mode == 1) {
                    $unit = $ratio > 0 ? round($basePrice * $ratio / 100, 2) : 0;
                } else {
                    $unit = self::getSupplyPrice($goods);
                }
                if ($unit <= 0) {
                    $source = 3; //取价为0 → 整单标记待复核
                }

                $lineAmount = round($unit * $num, 2);
                $amount    += $lineAmount;
                $baseTotal += round($basePrice * $num, 2);
                $detail[] = [
                    'goods_name'   => $goods['goods_name'],
                    'goods_num'    => $num,
                    //键名沿用 supply_price: 前端结算明细列硬编码展示 "@￥{supply_price} × 数量 = ￥{amount}"
                    //此处其语义即"单件结算单价", 不可改名, 否则前端将显示 undefined
                    'supply_price' => $unit,
                    'base_price'   => $basePrice, //单件成交价(供排查, 前端不展示)
                    'ratio'        => $ratio,     //本次计算所用比例(供排查, 前端不展示)
                    'amount'       => $lineAmount,
                ];
            }
            StoreSettlementOrder::create([
                'order_id' => $order['id'],
                'order_sn' => $order['sn'],
                'pay_store_id' => $payStoreId,
                'receive_store_id' => $receiveStoreId,
                'amount' => round($amount, 2),
                'base_price' => round($baseTotal, 2), //结算基数(成交价总额), 便于审计
                'ratio' => $ratio,                    //当时所用比例, 日后改比例也能还原口径
                'cost_source' => $source,
                'detail' => json_encode($detail, JSON_UNESCAPED_UNICODE),
                'status' => 0,
                'create_time' => time(),
            ]);
            return true;
        } catch (\Throwable $e) {
            //结算失败不阻断核销主流程(核销本身已成功), 但必须留下可定位的日志:
            //原实现只记 message, 缺少 order_id 与堆栈, 出问题无法定位到具体订单
            $orderId = (is_object($order) || is_array($order)) ? ($order['id'] ?? '未知') : '未知';
            Log::write(
                'store_settlement_error | order_id=' . $orderId
                . ' | ' . $e->getMessage()
                . ' | ' . $e->getFile() . ':' . $e->getLine()
                . ' | trace: ' . mb_substr($e->getTraceAsString(), 0, 2000),
                'error'
            );
            return true;
        }
    }

    /**
     * 兼容旧口径: 取商品协商价
     * 依次尝试 下单快照 supply_price → 规格表 supply_price → 规格表 cost_price, 均取不到返回 0
     * 仅当结算开关 mode=0 时使用
     */
    private static function getSupplyPrice(array $goods): float
    {
        $snapRaw = $goods['goods_snap'] ?? null;
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
        }
        return $supply;
    }

    /**
     * 结算中心: 生成汇总账单
     * period_type: 1=日 2=周 3=月
     *
     * 账期归属规则: 由「每条明细自身的 create_time」决定它属于哪个账期,
     *   而不是先按某个日期区间去筛明细。
     *
     * 为何这样改(修复周期漏账): 旧实现为"按给定日期区间捞明细 + 同账期同门店对
     *   已存在账单就跳过"。只要在账期中途生成过一次账单, 该账期之后新增的明细
     *   会被两边同时排除 —— 提交时因账单已存在被跳过, 下个账期又因 create_time
     *   不在新区间而捞不到 —— 形成永久漏账(且无任何告警)。
     *   现改为: 捞出全部未入账明细 → 各自归入所属账期 → 按(门店对+账期)分组
     *          → 该账期若已有"待确认"账单则追加, 否则新建。
     *   由此保证任何明细都不会漏掉, 也不会重复入账。
     *
     * 已确认/已付款的账单不会被追加(避免改动既定账务), 此时会另建一张补充账单,
     * 两张账单在列表中并存, 由人工核对后处理。
     *
     * @param int    $periodType 1日 2周 3月
     * @param string $date       仅作参数兼容与日期格式校验, 不再参与明细筛选
     * @param int    $adminId    操作人(后台管理员)
     * @return array
     */
    public static function generateBill($periodType, $date, $adminId)
    {
        $periodType = intval($periodType);
        if (!in_array($periodType, [1, 2, 3], true)) {
            $periodType = 1;
        }
        if ($date && !strtotime($date)) {
            return ['msg' => '日期格式不正确'];
        }

        Db::startTrans();
        try {
            //全部未入账明细; 不按账期时间过滤, 以避免漏账(原因见方法注释)
            $details = StoreSettlementOrder::where([
                ['status', '=', 0],
                ['settlement_id', '=', 0],
            ])->select()->toArray();
            if (empty($details)) {
                Db::rollback();
                return ['msg' => '暂无待结算明细'];
            }

            //按 (门店对 + 明细所属账期) 分组
            $groups = [];
            foreach ($details as $d) {
                //⚠️ StoreSettlementOrder 继承 BaseModel, 其时间字段经模型读取后会被自动
                //   格式化为 'Y-m-d H:i:s' 字符串(而非时间戳), 故此处必须兼容两种形态,
                //   不能直接 intval() —— intval('2026-09-15 10:00:00') 会得到 2026。
                $ctRaw   = $d['create_time'];
                $ctStamp = is_numeric($ctRaw) ? intval($ctRaw) : (strtotime((string) $ctRaw) ?: 0);
                list($startDate, $endDate) = self::getPeriodRange($ctStamp, $periodType);
                $key = $d['pay_store_id'] . '_' . $d['receive_store_id'] . '_' . $startDate . '_' . $endDate;
                if (!isset($groups[$key])) {
                    $groups[$key] = [
                        'pay_store_id'     => intval($d['pay_store_id']),
                        'receive_store_id' => intval($d['receive_store_id']),
                        'start_date'       => $startDate,
                        'end_date'         => $endDate,
                        'rows'             => [],
                    ];
                }
                $groups[$key]['rows'][] = $d;
            }

            $created  = [];
            $appended = [];
            foreach ($groups as $g) {
                //优先追加到同账期同门店对的"待确认"账单; 没有则新建(已确认/已付款的不动)
                $bill = StoreSettlement::where([
                    ['period_type', '=', $periodType],
                    ['start_date', '=', $g['start_date']],
                    ['end_date', '=', $g['end_date']],
                    ['pay_store_id', '=', $g['pay_store_id']],
                    ['receive_store_id', '=', $g['receive_store_id']],
                    ['status', '=', 0],
                ])->findOrEmpty();

                $isNew = $bill->isEmpty();
                if ($isNew) {
                    $bill = StoreSettlement::create([
                        'sn' => generate_sn((new StoreSettlement()), 'sn'),
                        'period_type' => $periodType,
                        'start_date' => $g['start_date'],
                        'end_date' => $g['end_date'],
                        'pay_store_id' => $g['pay_store_id'],
                        'receive_store_id' => $g['receive_store_id'],
                        'amount' => 0,
                        'order_count' => 0,
                        'status' => 0,
                        'create_admin_id' => intval($adminId),
                        'create_time' => time(),
                    ]);
                }

                $addAmount = '0';
                foreach ($g['rows'] as $r) {
                    $addAmount = bcadd($addAmount, (string) $r['amount'], 2);
                    StoreSettlementOrder::where('id', $r['id'])->update([
                        'settlement_id' => $bill->id,
                        'status' => 1,
                    ]);
                }
                //新建时为 0+本批; 追加时为 原值+本批
                StoreSettlement::where('id', $bill->id)->update([
                    'amount' => bcadd((string) $bill['amount'], $addAmount, 2),
                    'order_count' => intval($bill['order_count']) + count($g['rows']),
                ]);

                if ($isNew) {
                    $created[] = $bill->id;
                } else {
                    $appended[] = $bill->id;
                }
            }

            Db::commit();
            return [
                'created'  => $created,
                'appended' => $appended,
                'count'    => count($created) + count($appended),
            ];
        } catch (\Throwable $e) {
            Db::rollback();
            Log::write(
                'store_settlement_generate_error: ' . $e->getMessage()
                . ' | ' . $e->getFile() . ':' . $e->getLine(),
                'error'
            );
            return ['msg' => '生成账单失败: ' . $e->getMessage()];
        }
    }

    /**
     * 计算某时间戳所属的账期区间
     * @param int $timestamp
     * @param int $periodType 1日 2周(周一~周日) 3月
     * @return array [start_date, end_date], 格式 Y-m-d
     */
    private static function getPeriodRange(int $timestamp, int $periodType): array
    {
        switch ($periodType) {
            case 2:
                $start = strtotime(date('Y-m-d', strtotime('monday this week', $timestamp)));
                $end   = strtotime(date('Y-m-d', strtotime('sunday this week', $timestamp)));
                break;
            case 3:
                $start = strtotime(date('Y-m-01', $timestamp));
                $end   = strtotime(date('Y-m-t', $timestamp));
                break;
            default:
                $start = strtotime(date('Y-m-d', $timestamp));
                $end   = $start;
        }
        return [date('Y-m-d', $start), date('Y-m-d', $end)];
    }
}
