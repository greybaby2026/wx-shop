<?php

namespace app\adminapi\logic\goods;

use app\common\model\GoodsDeliveryTemplate;

class GoodsDeliveryTemplateLogic
{
     function add($data): bool
     {
         GoodsDeliveryTemplate::create([
             'name'         => $data['name'],
             'type'         => $data['type'],
             'content'      => $data['content'] ?? '',
             'content1'     => $data['content1'] ?? [],
         ]);
         
         return true;
     }
     
     function edit($data): bool
     {
         GoodsDeliveryTemplate::update([
             'name'         => $data['name'],
             'type'         => $data['type'],
             'content'      => $data['content'] ?? '',
             'content1'     => $data['content1'] ?? [],
         ],[ [ 'id', '=', $data['id'] ] ]);
    
         return true;
     }
     
     function delete($data)
     {
         GoodsDeliveryTemplate::destroy($data['id']);
         return true;
     }
     
     
}