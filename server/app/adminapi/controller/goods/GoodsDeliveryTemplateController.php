<?php

namespace app\adminapi\controller\goods;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\goods\GoodsDeliveryTemplateLists;
use app\adminapi\logic\goods\GoodsDeliveryTemplateLogic;
use app\adminapi\validate\goods\GoodsDeliveryTemplateValidate;

class GoodsDeliveryTemplateController extends BaseAdminController
{
    
    function lists()
    {
        return $this->dataLists(new GoodsDeliveryTemplateLists());
    }
    
    function add()
    {
        $params = (new GoodsDeliveryTemplateValidate())->post()->goCheck('add');
        (new GoodsDeliveryTemplateLogic())->add($params);
        return $this->success('添加成功', [], 1, 1);
    }
    
    function edit()
    {
        $params = (new GoodsDeliveryTemplateValidate())->post()->goCheck('edit');
        (new GoodsDeliveryTemplateLogic())->edit($params);
        return $this->success('修改成功', [], 1, 1);
    }
    
    function delete()
    {
        $params = (new GoodsDeliveryTemplateValidate())->post()->goCheck('delete');
        (new GoodsDeliveryTemplateLogic())->delete($params);
        return $this->success('删除成功', [], 1, 1);
    }
}