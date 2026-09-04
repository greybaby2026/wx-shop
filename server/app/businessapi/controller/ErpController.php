<?php
namespace app\businessapi\controller;

use app\businessapi\logic\ErpLogic;
use app\businessapi\validate\ErpValidate;
use app\common\service\ConfigService;

class ErpController extends BaseBusinesseController
{
    public array $notNeedLogin = ['userInfo', 'createOrder', 'confirmPay', 'sendCode'];

    private function checkApiKey(): bool
    {
        $apiKey = ConfigService::get('erp', 'api_key', '');
        if (empty($apiKey)) {
            return false;
        }
        $headerKey = request()->header('X-Api-Key', '');
        return !empty($headerKey) && $headerKey === $apiKey;
    }

    public function sendCode()
    {
        if (!$this->checkApiKey()) return $this->fail('API Key无效', [], 0, 0);
        $params = (new ErpValidate())->post()->goCheck('sendCode');
        $result = (new ErpLogic())->sendCode($params);
        if (false === $result) return $this->fail(ErpLogic::getError());
        return $this->success('验证码已发送', $result);
    }

    public function userInfo()
    {
        if (!$this->checkApiKey()) return $this->fail('API Key无效', [], 0, 0);
        $params = (new ErpValidate())->get()->goCheck('userInfo');
        $result = (new ErpLogic())->userInfo($params);
        return $this->data($result);
    }

    public function createOrder()
    {
        if (!$this->checkApiKey()) return $this->fail('API Key无效', [], 0, 0);
        $params = (new ErpValidate())->post()->goCheck('createOrder');
        $result = (new ErpLogic())->createOrder($params);
        if (false === $result) return $this->fail(ErpLogic::getError());
        return $this->success('下单成功', $result);
    }

    public function confirmPay()
    {
        if (!$this->checkApiKey()) return $this->fail('API Key无效', [], 0, 0);
        $params = (new ErpValidate())->post()->goCheck('confirmPay');
        $result = (new ErpLogic())->confirmPay($params);
        if (false === $result) return $this->fail(ErpLogic::getError());
        return $this->success('确认收款成功');
    }
}
