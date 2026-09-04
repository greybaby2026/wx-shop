<?php
namespace app\adminapi\controller\order;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\order\ErpOrderLists;
use app\adminapi\logic\order\ErpOrderLogic;
use app\adminapi\validate\order\OrderValidate;

class ErpOrderController extends BaseAdminController
{
    public function lists()
    {
        return $this->dataLists(new ErpOrderLists());
    }

    public function detail()
    {
        $params = (new OrderValidate())->goCheck('detail');
        $result = (new ErpOrderLogic())->detail($params);
        return $this->success('', $result);
    }
}
