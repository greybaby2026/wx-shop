<?php

namespace app\common\model;

use app\common\service\FileService;
use think\model\concern\SoftDelete;

class GoodsServiceGuarantee extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    
    function getIconAttr($fieldValue, $data)
    {
        return FileService::getFileUrl($fieldValue);
    }
    
    function setIconAttr($fieldValue, $data)
    {
        return FileService::setFileUrl($fieldValue);
    }
    
    
    static function getApiList($ids) : array
    {
        return static::withoutField([ 'delete_time', 'update_time' ])->order('id desc')->where('id', 'in', $ids)->select()->toArray();
    }
}