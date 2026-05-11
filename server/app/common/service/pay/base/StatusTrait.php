<?php

namespace app\common\service\pay\base;

trait StatusTrait
{
    protected bool $success = false;
    protected string $message = '';
    
    protected function setStatus(bool $success, string $message = '') : void
    {
        $this->success = $success;
        $this->message = $message;
    }
    
    function isSuccess() : bool
    {
        return $this->success;
    }
    
    function getMessage() : string
    {
        return $this->message;
    }
}