<?php
namespace app\businessapi\controller;

use app\businessapi\logic\StoreSettlementLogic;

/**
 * 门店结算账单控制器(门店端只读)
 *
 * 门店端仅需「看得见」本店应收/应付与明细; 账单的生成/确认/付款仍全部在后台结算中心完成。
 * 因此本控制器只提供 lists / detail 两个只读接口, 不提供任何写操作。
 *
 * Class StoreSettlementController
 * @package app\businessapi\controller
 */
class StoreSettlementController extends BaseBusinesseController
{
    /**
     * @notes 账单列表(仅本店相关: 应付 or 应收)
     * @return \think\response\Json
     */
    public function lists()
    {
        $params = $this->request->get();
        $logic = new StoreSettlementLogic();
        if ($logic->lists($params, $this->storeId)) {
            return $this->data(StoreSettlementLogic::$returnData);
        }
        return $this->fail(StoreSettlementLogic::$error);
    }

    /**
     * @notes 账单详情(含核销订单商品行明细)
     * @return \think\response\Json
     */
    public function detail()
    {
        $params = $this->request->get();
        $logic = new StoreSettlementLogic();
        if ($logic->detail($params, $this->storeId)) {
            return $this->data(StoreSettlementLogic::$returnData);
        }
        return $this->fail(StoreSettlementLogic::$error);
    }
}
