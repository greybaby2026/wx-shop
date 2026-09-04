<?php
namespace app\adminapi\controller;

use app\common\enum\AccountLogEnum;

class AccountLogController extends BaseAdminController
{
    public function lists()
    {
        return $this->dataLists();
    }

    public function getChangeType()
    {
        return $this->data(AccountLogEnum::getChangeTypeDesc('',true));
    }

    public function getBnwChangeType()
    {
        return $this->data(AccountLogEnum::getBnwChangeTypeDesc());
    }

    public function getIntegralChangeType()
    {
        return $this->data(AccountLogEnum::getIntegralChangeTypeDesc());
    }

    public function getBwChangeType()
    {
        return $this->data(AccountLogEnum::getBalanceChangeType());
    }

    public function getActChangeType()
    {
        return $this->data(AccountLogEnum::getBalanceChangeType());
    }
}
