<?php

namespace app\adminapi\logic\goods;

use app\common\logic\BaseLogic;
use app\common\model\GoodsServiceGuarantee;
use think\facade\Db;

class GoodsServiceGuaranteeLogic extends BaseLogic
{
    function add($params) : bool
    {
        GoodsServiceGuarantee::create($params,[ 'name', 'content' ]);
        
        return true;
    }
    
    function edit($params) : bool
    {
        GoodsServiceGuarantee::update($params, [ [ 'id', '=', $params['id'] ] ], [ 'name', 'content' ]);
        
        return true;
    }
    
    function delete($params) : bool
    {
        GoodsServiceGuarantee::destroy($params['id']);
    
        return true;
    }
    
}