<?php
namespace app\adminapi\controller\activity;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\activity\DeductActivityLogic;

class DeductActivityController extends BaseAdminController
{
    public function getConfig()
    {
        $result = DeductActivityLogic::getConfig();
        return $this->data($result);
    }

    public function setConfig()
    {
        $params = $this->request->post();
        DeductActivityLogic::setConfig($params);
        return $this->success('设置成功');
    }

    public function getThemePresets()
    {
        $result = DeductActivityLogic::getThemePresets();
        return $this->data($result);
    }
}
