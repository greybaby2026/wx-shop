<?php
namespace app\businessapi\logic;

use app\common\enum\StoreSettlementEnum;
use app\common\model\SelffetchShop;
use app\common\model\StoreSettlement;
use app\common\model\StoreSettlementOrder;

/**
 * 门店端结算账单(只读)
 *
 * 数据口径与后台「门店结算中心」完全一致(同一批 ls_store_settlement / ls_store_settlement_order),
 * 本类只做「按当前门店身份过滤 + 展示字段补全」, 不产生任何写操作。
 *
 * 可见范围:
 *   门店账号(store_id > 0): 仅可见与本店相关的账单 —— 本店作为付款方(应付)或收款方(应收)
 *   平台账号(store_id = 0): 不参与门店间结算, 不返回任何账单(前端给出提示)
 *
 * 口径说明(与 StoreSettlementLogic 生成逻辑一致, 不一致会导致加盟商对账失败):
 *   pay_store_id     = 归属门店 = 收了顾客钱、需向对方付款的一方 → 本店的「应付」
 *   receive_store_id = 核销门店 = 实际交货、应收钱的一方       → 本店的「应收」
 */
class StoreSettlementLogic
{
    public static $returnData = [];
    public static $error = '';

    /** 默认每页条数 */
    const DEFAULT_PAGE_SIZE = 20;

    /**
     * @notes 账单列表(仅本店相关)
     * @param array $params status / role / start_date / end_date / page_no / page_size
     * @param int $storeId 当前登录账号所属门店(0=平台账号)
     * @return bool
     */
    public function lists(array $params, int $storeId): bool
    {
        //平台账号不参与门店结算: 返回空列表并带标识, 由前端给出明确提示
        if ($storeId <= 0) {
            self::$returnData = [
                'lists' => [],
                'count' => 0,
                'page_no' => 1,
                'page_size' => self::DEFAULT_PAGE_SIZE,
                'is_platform_account' => 1,
                'stat' => self::emptyStat(),
            ];
            return true;
        }

        $role = in_array($params['role'] ?? '', ['pay', 'receive'], true) ? $params['role'] : '';
        $page = max(1, intval($params['page_no'] ?? 1));
        $size = max(1, intval($params['page_size'] ?? self::DEFAULT_PAGE_SIZE));

        $lists = $this->buildQuery($params, $storeId, $role)
            ->order('id desc')
            ->limit(($page - 1) * $size, $size)
            ->select()->toArray();
        $count = $this->buildQuery($params, $storeId, $role)->count();

        self::fillStoreNames($lists);
        self::appendRole($lists, $storeId);
        foreach ($lists as &$row) {
            $row['status_desc'] = StoreSettlementEnum::getStatusDesc($row['status']);
            $row['period_type_desc'] = StoreSettlementEnum::getPeriodDesc($row['period_type']);
            $row['amount'] = self::money($row['amount'] ?? 0);
            $row['create_time'] = self::formatTime($row['create_time'] ?? '');
            $row['confirm_time'] = self::formatTime($row['confirm_time'] ?? '');
            $row['pay_time'] = self::formatTime($row['pay_time'] ?? '');
        }
        unset($row);

        self::$returnData = [
            'lists' => $lists,
            'count' => $count,
            'page_no' => $page,
            'page_size' => $size,
            'is_platform_account' => 0,
            'stat' => $this->stat($params, $storeId),
        ];
        return true;
    }

    /**
     * @notes 账单详情(含商品行明细)
     * @param array $params id
     * @param int $storeId
     * @return bool
     */
    public function detail(array $params, int $storeId): bool
    {
        $id = intval($params['id'] ?? 0);
        if ($id <= 0) {
            self::$error = '参数错误';
            return false;
        }
        if ($storeId <= 0) {
            self::$error = '当前为平台账号，不参与门店结算，无账单数据';
            return false;
        }

        $bill = StoreSettlement::findOrEmpty($id)->toArray();
        if (empty($bill)) {
            self::$error = '账单不存在';
            return false;
        }
        //越权校验: 账单必须与本店相关(既能防猜 ID, 也避免跨店账务泄露)
        if (intval($bill['pay_store_id']) !== $storeId && intval($bill['receive_store_id']) !== $storeId) {
            self::$error = '无权查看该账单';
            return false;
        }

        $details = StoreSettlementOrder::where('settlement_id', $id)
            ->order('id asc')
            ->select()->toArray();
        foreach ($details as &$d) {
            $d['detail'] = json_decode((string)$d['detail'], true) ?: [];
            $d['cost_source_desc'] = StoreSettlementEnum::getCostSourceDesc($d['cost_source']);
            $d['amount'] = self::money($d['amount'] ?? 0);
            $d['base_price'] = self::money($d['base_price'] ?? 0);
            $d['create_time'] = self::formatTime($d['create_time'] ?? '');
        }
        unset($d);

        $billRows = [$bill];
        self::fillStoreNames($billRows);
        self::appendRole($billRows, $storeId);
        $bill = $billRows[0];
        $bill['status_desc'] = StoreSettlementEnum::getStatusDesc($bill['status']);
        $bill['period_type_desc'] = StoreSettlementEnum::getPeriodDesc($bill['period_type']);
        $bill['amount'] = self::money($bill['amount'] ?? 0);
        $bill['create_time'] = self::formatTime($bill['create_time'] ?? '');
        $bill['confirm_time'] = self::formatTime($bill['confirm_time'] ?? '');
        $bill['pay_time'] = self::formatTime($bill['pay_time'] ?? '');

        self::$returnData = ['bill' => $bill, 'details' => $details];
        return true;
    }

    /**
     * @notes 构建查询(条件与后台结算中心一致, 额外加「本店相关」隔离)
     * @param array $params
     * @param int $storeId
     * @param string $role '' = 应收+应付 | pay = 仅本店应付 | receive = 仅本店应收
     * @return \think\db\Query
     */
    private function buildQuery(array $params, int $storeId, string $role = '')
    {
        $where = [];
        if (isset($params['status']) && $params['status'] !== '') {
            $where[] = ['status', '=', intval($params['status'])];
        }
        //账期区间过滤: 与后台列表同口径(账单同时含 start_date / end_date)
        if (!empty($params['start_date'])) {
            $where[] = ['end_date', '>=', $params['start_date']];
        }
        if (!empty($params['end_date'])) {
            $where[] = ['start_date', '<=', $params['end_date']];
        }

        $query = StoreSettlement::where($where);
        if ($role === 'pay') {
            $query->where('pay_store_id', $storeId);
        } elseif ($role === 'receive') {
            $query->where('receive_store_id', $storeId);
        } else {
            //门店隔离: 用闭包保证 OR 被括号包裹, 不与上面的 AND 条件混淆
            $query->where(function ($q) use ($storeId) {
                $q->where('pay_store_id', $storeId)->whereOr('receive_store_id', $storeId);
            });
        }
        return $query;
    }

    /**
     * @notes 汇总(应收/应付/待确认账单数), 与列表共享除分页外的筛选条件
     *        说明: 汇总不跟随 role 筛选, 始终同时给出应收与应付, 便于一眼看清两向金额
     * @param array $params
     * @param int $storeId
     * @return array
     */
    private function stat(array $params, int $storeId): array
    {
        if ($storeId <= 0) {
            return self::emptyStat();
        }
        $stat = self::emptyStat();
        //应收: 本店作为收款方(核销门店)
        $stat['receive_total'] = self::money(
            $this->buildQuery($params, $storeId, 'receive')->sum('amount')
        );
        //应付: 本店作为付款方(归属门店)
        $stat['pay_total'] = self::money(
            $this->buildQuery($params, $storeId, 'pay')->sum('amount')
        );
        //待确认账单数: 不受状态筛选影响, 固定统计「待确认」
        $waitParams = $params;
        $waitParams['status'] = StoreSettlementEnum::STATUS_WAIT_CONFIRM;
        $stat['wait_confirm_count'] = $this->buildQuery($waitParams, $storeId)->count();
        return $stat;
    }

    private static function emptyStat(): array
    {
        return [
            'receive_total' => '0.00',
            'pay_total' => '0.00',
            'wait_confirm_count' => 0,
        ];
    }

    /**
     * @notes 补全付款门店/收款门店名称
     */
    private static function fillStoreNames(array &$rows): void
    {
        $shopIds = [];
        foreach ($rows as $row) {
            $shopIds[] = intval($row['pay_store_id'] ?? 0);
            $shopIds[] = intval($row['receive_store_id'] ?? 0);
        }
        $names = SelffetchShop::getNameMap($shopIds);
        foreach ($rows as &$row) {
            $payId = intval($row['pay_store_id'] ?? 0);
            $receiveId = intval($row['receive_store_id'] ?? 0);
            $row['pay_store_name'] = $names[$payId] ?? ($payId > 0 ? '门店' . $payId : '');
            $row['receive_store_name'] = $names[$receiveId] ?? ($receiveId > 0 ? '门店' . $receiveId : '');
        }
        unset($row);
    }

    /**
     * @notes 标注本店在这张账单中的角色(应收 / 应付)与对方门店
     */
    private static function appendRole(array &$rows, int $storeId): void
    {
        foreach ($rows as &$row) {
            $isPay = intval($row['pay_store_id'] ?? 0) === $storeId;
            $row['role'] = $isPay ? 'pay' : 'receive';
            $row['role_desc'] = $isPay ? '应付' : '应收';
            $row['other_store_name'] = $isPay
                ? ($row['receive_store_name'] ?? '')
                : ($row['pay_store_name'] ?? '');
        }
        unset($row);
    }

    /**
     * @notes 金额统一为两位小数字符串(避免浮点显示误差)
     */
    private static function money($value): string
    {
        return number_format((float)$value, 2, '.', '');
    }

    /**
     * @notes 时间字段容错格式化
     *        StoreSettlement 经模型读取后 create_time 可能已是 'Y-m-d H:i:s' 字符串
     *        (config/database.php datetime_format), 也可能仍是时间戳, 两种都要兼容
     */
    private static function formatTime($raw): string
    {
        if (empty($raw)) {
            return '';
        }
        if (is_numeric($raw)) {
            return date('Y-m-d H:i:s', intval($raw));
        }
        return (string)$raw;
    }
}
