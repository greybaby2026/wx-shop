<?php

namespace app\adminapi\validate\goods;

use app\common\model\Goods;
use app\common\validate\BaseValidate;

class GoodsDeliveryTemplateValidate extends BaseValidate
{
    protected $rule = [
        'id'        => [ 'require' ],
        'name'      => [ 'require' ],
        'type'      => [ 'require', 'in' => [ 0, 1 ] ],
        'content'   => [ 'requireIf' => 'type,0', 'max' => 255 ],
        'content1'  => [ 'requireIf' => 'type,1' ],
    ];
    
    protected $field = [
        'id'        => 'ID',
        'name'      => '名称',
        'type'      => '发货类型',
        'content'   => '固定内容',
        'content1'  => '自定义内容',
    ];
    
    function sceneAdd()
    {
        return $this->only([ 'name', 'type', 'content', 'content1' ]);
    }
    
    function sceneEdit()
    {
        return $this->only([ 'id', 'name', 'type', 'content', 'content1' ]);
    }
    
    function sceneDetail()
    {
        return $this->only([ 'id' ]);
    }
    
    function sceneDelete()
    {
        return $this->only([ 'id' ])->append('id', 'checkId');
    }
    
    function checkId($fieldValue, $rule, $data)
    {
        if (Goods::where('delivery_template_id', $data['id'])->value('id')) {
            return '存在商品使用此模版 不能删除';
        }
        
        return true;
    }
}