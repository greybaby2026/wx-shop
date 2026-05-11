<?php

namespace app\adminapi\controller\marketing;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\validate\marketing\RegisterAwardValidate;
use app\common\logic\RegisterAwardLogic;

class RegisterAwardController extends BaseAdminController
{
    /**
     * @notes 注册奖励获取配置
     * @return \think\response\Json
     * @author lbzy
     * @datetime 2024-06-17 09:57:41
     */
    function getConfig()
    {
        return $this->data(RegisterAwardLogic::getConfig());
    }
    
    /**
     * @notes 注册奖励保存配置
     * @return \think\response\Json
     * @author lbzy
     * @datetime 2024-06-17 09:57:41
     */
    function setConfig()
    {
        $params = (new RegisterAwardValidate())->post()->goCheck('set');
        RegisterAwardLogic::setConfig($params);
        return $this->success('成功', [], 1, 1);
    }
}