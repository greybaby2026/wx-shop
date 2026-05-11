<?php

namespace app\openapi\controller;

use app\common\controller\BaseLikeShopController;
use app\openapi\logic\OpenApiLogic;

class BaseOpenController extends BaseLikeShopController
{
    protected string $appKey = '';
    protected array $requestData = [];

    public function initialize()
    {
        $this->appKey = $this->request->header('app-key', '');
        $this->requestData = $this->request->param();
    }
}
