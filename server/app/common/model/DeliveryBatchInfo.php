<?php

namespace app\common\model;

use think\model\concern\SoftDelete;

class DeliveryBatchInfo extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;
    
    protected $autoWriteTimestamp = true;
    
}