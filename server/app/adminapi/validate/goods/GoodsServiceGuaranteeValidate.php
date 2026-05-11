<?php

namespace app\adminapi\validate\goods;

use app\common\validate\BaseValidate;

class GoodsServiceGuaranteeValidate extends BaseValidate
{
    protected $rule = [
        'id'        => [ 'require', 'integer' ],
        'name'      => [ 'require', 'max' => 50 ],
        'icon'      => [ 'require', 'max' => 255 ],
        'content'   => [ 'require', 'max' => 65535 ],
        'sort'      => [ 'require', 'number' ],
        'status'    => [ 'require', 'in' => [ 0, 1 ] ],
    ];
    
    protected $field = [
        'id'        => 'id',
        'name'      => '名称',
        'icon'      => '图标',
        'content'   => '说明',
        'sort'      => '排序',
        'status'    => '状态',
    ];
    
    protected $message = [
    
    ];
    
    function sceneAdd()
    {
        return $this->only([ 'name', 'content' ]);
    }
    
    function sceneEdit()
    {
        return $this->only([ 'id', 'name', 'content' ]);
    }
    
    function sceneDelete()
    {
        return $this->only([ 'id' ]);
    }
}