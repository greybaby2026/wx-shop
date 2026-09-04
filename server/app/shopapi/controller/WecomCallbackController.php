<?php

namespace app\shopapi\controller;

use app\common\service\wecom\KefuCallbackService;

class WecomCallbackController extends BaseShopController
{
    public array $notNeedLogin = ['receive'];

    public function isNotNeedLogin()
    {
        return true;
    }

    public function receive()
    {
        $kefuService = new KefuCallbackService();
        $kefuService->handleMessage();
        return response('success');
    }
}
