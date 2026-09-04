<?php
namespace app\adminapi\logic\store_settlement;

use app\common\logic\StoreSettlementLogic;
use app\common\model\StoreSettlement;
use app\common\model\StoreSettlementOrder;

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
        foreach ($lists as &$row) {
            $row['status_desc'] = [0 => '待确认', 1 => '已确认', 2 => '已付款'][$row['status']] ?? '未知';
            $row['period_type_desc'] = [1 => '日', 2 => '周', 3 => '月'][$row['period_type']] ?? '日';
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
        }
        $bill['status_desc'] = [0 => '待确认', 1 => '已确认', 2 => '已付款'][$bill['status']] ?? '未知';
        $bill['period_type_desc'] = [1 => '日', 2 => '周', 3 => '月'][$bill['period_type']] ?? '日';
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
        if ($bill->status != 0) {
            self::$error = '账单状态不允许确认';
            return false;
        }
        $bill->status = 1;
        $bill->confirm_time = time();
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
        if ($bill->status != 1) {
            self::$error = '请先确认账单';
            return false;
        }
        $bill->status = 2;
        $bill->pay_time = time();
        $bill->save();
        return true;
    }
}
