<?php

namespace app\adminapi\controller\goods;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\goods\GoodsServiceGuaranteeLogic;
use app\adminapi\validate\goods\GoodsServiceGuaranteeValidate;

class GoodsServiceGuaranteeController extends BaseAdminController
{
    /**
     * @notes 服务保障列表
     * @return \think\response\Json
     * @author lbzy
     * @datetime 2023-08-18 17:54:29
     */
    function lists()
    {
        return $this->dataLists();
    }
    
    function add()
    {
        $params = (new GoodsServiceGuaranteeValidate())->post()->goCheck('add');
    
        $res = (new GoodsServiceGuaranteeLogic)->add($params);
        
        if (true === $res) {
            return $this->success('添加成功',[],1,1);
        }
        
        return $this->fail($res);
    }
    
    function edit()
    {
        $params = (new GoodsServiceGuaranteeValidate())->post()->goCheck('edit');
    
        $res = (new GoodsServiceGuaranteeLogic)->edit($params);
    
        if (true === $res) {
            return $this->success('编辑成功',[],1,1);
        }
    
        return $this->fail($res);
    }
    
    function delete()
    {
        $params = (new GoodsServiceGuaranteeValidate())->post()->goCheck('delete');
        
        $res = (new GoodsServiceGuaranteeLogic)->delete($params);
        
        if (true === $res) {
            return $this->success('删除成功',[],1,1);
        }
        
        return $this->fail($res);
    }
}