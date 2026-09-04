<?php

namespace app\shopapi\controller;

use app\common\service\wecom\MnpKefuService;

class MnpKefuController extends BaseShopController
{
    public array $notNeedLogin = ['receive'];

    public function isNotNeedLogin()
    {
        return true;
    }

    public function receive()
    {
        $echostr = input('echostr', '');
        if (!empty($echostr)) {
            echo $echostr;
            exit;
        }

        return response('success');
    }
}
