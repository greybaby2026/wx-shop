<?php
namespace app\shopapi\controller;

use app\shopapi\logic\activity\DeductService;

class ActivityController extends BaseShopController
{
    public array $notNeedLogin = ['getDisplayInfo'];

    public function getDisplayInfo()
    {
        $info = DeductService::getDisplayInfo();
        return $this->success('', $info);
    }
}
