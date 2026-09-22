<?php
namespace app\adminapi\logic\store_settlement;

use app\common\enum\StoreSettlementEnum;
use app\common\logic\StoreSettlementLogic;
use app\common\model\SelffetchShop;
use app\common\model\StoreSettlement;
use app\common\model\StoreSettlementOrder;
use app\common\service\ConfigService;

class SettlementLogic
{
    public static $returnData = [];
    public static $error = '';

    /**
     * 生成汇总账单
     */
    public function generate($params)
    {
        $result = StoreSettlementLogic::generateBill(
            intval($params['period_type']),
            $params['date'],
            $params['admin_id']
        );
        if (isset($result['msg'])) {
            self::$error = $result['msg'];
            return false;
        }
        self::$returnData = $result;
        return true;
    }

    /**
     * 账单列表
     */
    public function lists($params)
    {
        $where = [];
        if (isset($params['status']) && $params['status'] !== '') {
            $where[] = ['status', '=', intval($params['status'])];
        }
        if (!empty($params['start_date'])) {
            $where[] = ['start_date', '>=', $params['start_date']];
        }
        if (!empty($params['end_date'])) {
            $where[] = ['end_date', '<=', $params['end_date']];
        }
        $query = StoreSettlement::where($where)->order('id desc');
        $count = $query->count();
        $page = max(1, intval($params['page_no'] ?? 1));
        $size = max(1, intval($params['page_size'] ?? 20));
        $lists = $query->limit(($page - 1) * $size, $size)->select()->toArray();
        $lists = self::fillStoreNames($lists);
        foreach ($lists as &$row) {
            $row['status_desc'] = StoreSettlementEnum::getStatusDesc($row['status']);
            $row['period_type_desc'] = StoreSettlementEnum::getPeriodDesc($row['period_type']);
        }
        self::$returnData = ['lists' => $lists, 'count' => $count, 'page_no' => $page, 'page_size' => $size];
        return true;
    }

    /**
     * 账单详情(含明细)
     */
    public function detail($params)
    {
        $bill = StoreSettlement::findOrEmpty(intval($params['id']))->toArray();
        if (empty($bill)) {
            self::$error = '账单不存在';
            return false;
        }
        $details = StoreSettlementOrder::where('settlement_id', $bill['id'])->select()->toArray();
        foreach ($details as &$d) {
            $d['detail'] = json_decode($d['detail'], true);
            $d['cost_source_desc'] = StoreSettlementEnum::getCostSourceDesc($d['cost_source']);
        }
        $billArr = self::fillStoreNames([$bill]);
        $bill = $billArr[0];
        $bill['status_desc'] = StoreSettlementEnum::getStatusDesc($bill['status']);
        $bill['period_type_desc'] = StoreSettlementEnum::getPeriodDesc($bill['period_type']);
        self::$returnData = ['bill' => $bill, 'details' => $details];
        return true;
    }

    /**
     * 确认账单
     */
    public function confirm($params)
    {
        $bill = StoreSettlement::findOrEmpty(intval($params['id']));
        if ($bill->isEmpty()) {
            self::$error = '账单不存在';
            return false;
        }
        if ($bill->status != StoreSettlementEnum::STATUS_WAIT_CONFIRM) {
            self::$error = '账单状态不允许确认';
            return false;
        }
        $bill->status = StoreSettlementEnum::STATUS_CONFIRMED;
        $bill->confirm_time = time();
        $bill->confirm_admin_id = intval($params['admin_id'] ?? 0);   //操作人留痕
        $bill->save();
        return true;
    }

    /**
     * 标记付款
     */
    public function markPaid($params)
    {
        $bill = StoreSettlement::findOrEmpty(intval($params['id']));
        if ($bill->isEmpty()) {
            self::$error = '账单不存在';
            return false;
        }
        if ($bill->status != StoreSettlementEnum::STATUS_CONFIRMED) {
            self::$error = '请先确认账单';
            return false;
        }

        //付款前一致性校验：明细须与生成账单时一致，避免漏付
        $details = StoreSettlementOrder::where('settlement_id', $bill->id)->select()->toArray();
        if (count($details) !== intval($bill->order_count)) {
            self::$error = '账单明细不完整（实挂 ' . count($details) . ' 条，应为 ' . intval($bill->order_count) . ' 条），请重新生成账单';
            return false;
        }
        $detailAmount = '0';
        foreach ($details as $d) {
            $detailAmount = bcadd($detailAmount, (string)$d['amount'], 2);
        }
        if (bccomp($detailAmount, (string)$bill->amount, 2) !== 0) {
            self::$error = '账单金额与明细合计不一致（账单 ' . $bill->amount . '，明细合计 ' . $detailAmount . '），请核对后重试';
            return false;
        }

        //0 元账单无付款意义，直接拦截（通常为结算单价取价失败所致）
        if (bccomp((string)$bill->amount, '0', 2) <= 0) {
            self::$error = '账单金额为 0，无法标记付款，请先核对结算单价配置';
            return false;
        }

        $bill->status = StoreSettlementEnum::STATUS_PAID;
        $bill->pay_time = time();
        $bill->pay_admin_id = intval($params['admin_id'] ?? 0);   //操作人留痕
        $bill->save();
        return true;
    }

    /**
     * @notes 结算配置(结算开关/全局比例/门店独立比例)
     * @return bool
     */
    public function config()
    {
        $mode  = intval(ConfigService::get('store_settlement', 'mode', 1));
        $ratio = floatval(ConfigService::get('store_settlement', 'ratio', 0));
        $shops = SelffetchShop::field('id,name,contact,settlement_ratio,status')
            ->order('id asc')
            ->select()
            ->toArray();
        foreach ($shops as &$s) {
            $s['settlement_ratio'] = floatval($s['settlement_ratio']);
        }
        unset($s);
        self::$returnData = [
            'mode'  => $mode,
            'ratio' => $ratio,
            'shops' => $shops,
        ];
        return true;
    }

    /**
     * @notes 保存结算配置
     *        先整体校验、后统一写入, 避免出现"部分字段已改"的半成功状态
     * @param $params
     * @return bool
     */
    public function saveConfig($params)
    {
        $mode  = intval($params['mode'] ?? 1);
        $ratio = floatval($params['ratio'] ?? 0);
        if (!in_array($mode, [0, 1], true)) {
            self::$error = '结算模式取值不合法';
            return false;
        }
        if ($ratio < 0 || $ratio > 100) {
            self::$error = '全局比例需在 0 ~ 100 之间';
            return false;
        }

        //门店比例: 先全部校验
        $shopRows = [];
        foreach ((array) ($params['shops'] ?? []) as $row) {
            $sid = intval($row['id'] ?? 0);
            if ($sid <= 0) {
                continue;
            }
            $r = floatval($row['settlement_ratio'] ?? 0);
            if ($r < 0 || $r > 100) {
                self::$error = '门店(ID:' . $sid . ')的比例需在 0 ~ 100 之间';
                return false;
            }
            $shopRows[$sid] = $r;
        }

        //全部校验通过后再写入
        ConfigService::set('store_settlement', 'mode', $mode);
        ConfigService::set('store_settlement', 'ratio', $ratio);
        foreach ($shopRows as $sid => $r) {
            $shop = SelffetchShop::findOrEmpty($sid);
            if ($shop->isEmpty()) {
                continue;
            }
            $shop->settlement_ratio = $r;
            $shop->save();
        }
        return true;
    }

    private static function fillStoreNames(array $rows): array
    {
        $shopIds = [];
        foreach ($rows as $r) {
            $shopIds[] = $r['pay_store_id'];
            $shopIds[] = $r['receive_store_id'];
        }
        $shopIds = array_unique(array_filter($shopIds));
        $names = \app\common\model\SelffetchShop::whereIn('id', $shopIds ?: [0])->column('name', 'id');
        foreach ($rows as &$r) {
            $r['pay_store_name'] = $names[$r['pay_store_id']] ?? ('门店' . $r['pay_store_id']);
            $r['receive_store_name'] = $names[$r['receive_store_id']] ?? ('门店' . $r['receive_store_id']);
        }
        return $rows;
    }
}
