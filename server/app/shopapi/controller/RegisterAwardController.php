<?php

namespace app\shopapi\controller;

use app\common\logic\RegisterAwardLogic;

class RegisterAwardController extends BaseShopController
{
    public array $notNeedLogin = [ 'getConfig' ];
    
    function getConfig()
    {
        return $this->success('', RegisterAwardLogic::getConfig());
    }
}