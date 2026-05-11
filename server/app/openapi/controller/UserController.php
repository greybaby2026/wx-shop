<?php

namespace app\openapi\controller;

use app\openapi\logic\OpenApiLogic;

class UserController extends BaseOpenController
{
    public function orderAmount()
    {
        $params = $this->request->param();
        $result = OpenApiLogic::getUserOrderAmount($params);
        if ($result === false) {
            return $this->fail(OpenApiLogic::getError());
        }
        return $this->data($result);
    }

    public function distributionRelation()
    {
        $params = $this->request->param();
        $result = OpenApiLogic::getDistributionRelation($params);
        if ($result === false) {
            return $this->fail(OpenApiLogic::getError());
        }
        return $this->data($result);
    }

    public function commission()
    {
        $params = $this->request->param();
        $result = OpenApiLogic::getDistributionCommission($params);
        if ($result === false) {
            return $this->fail(OpenApiLogic::getError());
        }
        return $this->data($result);
    }

    public function fans()
    {
        $params = $this->request->param();
        $result = OpenApiLogic::getFansList($params);
        if ($result === false) {
            return $this->fail(OpenApiLogic::getError());
        }
        return $this->data($result);
    }

    public function info()
    {
        $params = $this->request->param();
        $result = OpenApiLogic::getUserInfo($params);
        if ($result === false) {
            return $this->fail(OpenApiLogic::getError());
        }
        return $this->data($result);
    }

    public function list()
    {
        $params = $this->request->param();
        $result = OpenApiLogic::getUserList($params);
        if ($result === false) {
            return $this->fail(OpenApiLogic::getError());
        }
        return $this->data($result);
    }

    public function validOrderAmount()
    {
        $params = $this->request->param();
        $result = OpenApiLogic::getUserValidOrderAmount($params);
        if ($result === false) {
            return $this->fail(OpenApiLogic::getError());
        }
        return $this->data($result);
    }

    public function commissionStats()
    {
        $params = $this->request->param();
        $result = OpenApiLogic::getUserCommissionStats($params);
        if ($result === false) {
            return $this->fail(OpenApiLogic::getError());
        }
        return $this->data($result);
    }

    public function refundList()
    {
        $params = $this->request->param();
        $result = OpenApiLogic::getRefundList($params);
        if ($result === false) {
            return $this->fail(OpenApiLogic::getError());
        }
        return $this->data($result);
    }

    public function refundDetail()
    {
        $params = $this->request->param();
        $result = OpenApiLogic::getRefundDetail($params);
        if ($result === false) {
            return $this->fail(OpenApiLogic::getError());
        }
        return $this->data($result);
    }

    public function refundStatistics()
    {
        $params = $this->request->param();
        $result = OpenApiLogic::getUserRefundStatistics($params);
        if ($result === false) {
            return $this->fail(OpenApiLogic::getError());
        }
        return $this->data($result);
    }

    public function orderList()
    {
        $params = $this->request->param();
        $result = OpenApiLogic::getUserOrderList($params);
        if ($result === false) {
            return $this->fail(OpenApiLogic::getError());
        }
        return $this->data($result);
    }

    public function rechargeCommission()
    {
        $params = $this->request->param();
        $result = OpenApiLogic::getRechargeCommission($params);
        if ($result === false) {
            return $this->fail(OpenApiLogic::getError());
        }
        return $this->data($result);
    }
}
