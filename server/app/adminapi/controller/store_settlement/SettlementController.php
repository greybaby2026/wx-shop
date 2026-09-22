<?php
namespace app\adminapi\controller\store_settlement;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\store_settlement\SettlementLogic;

class SettlementController extends BaseAdminController
{
    /**
     * @notes 生成汇总账单
     */
    public function generate()
    {
        $params = $this->request->post();
        $params['period_type'] = intval($params['period_type'] ?? 1);
        $params['date'] = $params['date'] ?? date('Y-m-d');
        $params['admin_id'] = $this->adminId;
        $logic = new SettlementLogic();
        if ($logic->generate($params)) {
            return $this->success('生成成功', SettlementLogic::$returnData);
        }
        return $this->fail(SettlementLogic::$error);
    }

    /**
     * @notes 账单列表
     */
    public function lists()
    {
        $params = $this->request->get();
        $logic = new SettlementLogic();
        if ($logic->lists($params)) {
            return $this->data(SettlementLogic::$returnData);
        }
        return $this->fail(SettlementLogic::$error);
    }

    /**
     * @notes 账单详情
     */
    public function detail()
    {
        $params = $this->request->get();
        $logic = new SettlementLogic();
        if ($logic->detail($params)) {
            return $this->data(SettlementLogic::$returnData);
        }
        return $this->fail(SettlementLogic::$error);
    }

    /**
     * @notes 确认账单
     */
    public function confirm()
    {
        $params = $this->request->post();
        $params['admin_id'] = $this->adminId;   //操作人留痕(服务端取,前端无需传)
        $logic = new SettlementLogic();
        if ($logic->confirm($params)) {
            return $this->success('确认成功', [], 1, 1);
        }
        return $this->fail(SettlementLogic::$error);
    }

    /**
     * @notes 标记付款
     */
    public function markPaid()
    {
        $params = $this->request->post();
        $params['admin_id'] = $this->adminId;   //操作人留痕(服务端取,前端无需传)
        $logic = new SettlementLogic();
        if ($logic->markPaid($params)) {
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(SettlementLogic::$error);
    }

    /**
     * @notes 结算配置(结算开关/全局比例/门店独立比例)
     */
    public function config()
    {
        $logic = new SettlementLogic();
        if ($logic->config()) {
            return $this->data(SettlementLogic::$returnData);
        }
        return $this->fail(SettlementLogic::$error);
    }

    /**
     * @notes 保存结算配置
     */
    public function saveConfig()
    {
        $params = $this->request->post();
        $logic = new SettlementLogic();
        if ($logic->saveConfig($params)) {
            return $this->success('保存成功', [], 1, 1);
        }
        return $this->fail(SettlementLogic::$error);
    }
}
