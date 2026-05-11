<?php

namespace app\adminapi\logic\order;

use app\adminapi\validate\order\OrderValidate;
use app\common\enum\DeliveryEnum;
use app\common\enum\OrderEnum;
use app\common\model\AddressLibrary;
use app\common\model\Order;
use app\common\model\DeliveryBatch;
use app\common\model\DeliveryBatchInfo;
use app\common\model\Express;
use app\common\model\OrderGoods;
use think\facade\Db;

class OrderDeliveryBatchLogic
{
    static function lists(array $params)
    {
        $lists   = DeliveryBatch::page($params['page_no'] ?? 1)->order('id desc')->limit($params['page_size'] ?? 10)->select()->toArray();
        $count  = DeliveryBatch::count();
        
        return [ 'count' => $count, 'lists' => $lists ];
    }
    
    static function getFailInfoLists($id)
    {
        return DeliveryBatchInfo::where('batch_id', $id)->where('status', 2)->select()->toArray();
    }
    
    static function detail($id)
    {
        $detail = DeliveryBatch::where('id', $id)->append([ 'progress' ])->find();
        
        return $detail ? $detail->toArray() : [];
    }
    
    static function delivery($detail)
    {
        if ($detail['status'] == 1) {
            return true;
        }
        $infoLists = DeliveryBatchInfo::where('batch_id', $detail['id'])->select()->toArray();
        foreach ($infoLists as $info) {
            // 订单
            $order = Order::where('sn', $info['sn'])->find();
            if (empty($order['id'])) {
                DeliveryBatch::update([ 'fail' => Db::raw('fail+1') ], [ [ 'id' ,'=', $detail['id'] ] ]);
                DeliveryBatchInfo::update([ 'status' => 2, 'fail_content' => '订单不存在' ], [ [ 'id', '=', $info['id'] ] ]);
                continue;
            }
            // 部分发货订单不能批量发货
            if ($order['express_status'] == DeliveryEnum::PART_SHIPPED) {
                DeliveryBatch::update([ 'fail' => Db::raw('fail+1') ], [ [ 'id' ,'=', $detail['id'] ] ]);
                DeliveryBatchInfo::update([ 'status' => 2, 'fail_content' => '订单已部分发货，不能批量发货' ], [ [ 'id', '=', $info['id'] ] ]);
                continue;
            }
            // 快递
            $express = Express::where('name', $info['express_name'])->find();
            if (empty($express['id'])) {
                DeliveryBatch::update([ 'fail' => Db::raw('fail+1') ], [ [ 'id' ,'=', $detail['id'] ] ]);
                DeliveryBatchInfo::update([ 'status' => 2, 'fail_content' => '快递公司不存在' ], [ [ 'id', '=', $info['id'] ] ]);
                continue;
            }
            // 检测
            $validate = new OrderValidate;
            if ($order['order_status'] == OrderEnum::STATUS_WAIT_DELIVERY) {
                // 待发货
                $orderGoods = OrderGoods::where(['order_id'=>$order['id']])->select()->toArray();
                $orderGoodsInfo = [];
                foreach ($orderGoods as $value) {
                    $orderGoodsInfo[] = [
                        'order_goods_id' => $value['id'],
                        'delivery_num' => $value['goods_num']
                    ];
                }
                $data = [
                    'id'            => $order['id'],
                    'send_type'     => 1,
                    'admin_id'      => request()->adminInfo['admin_id'],
                    'delivery_address_id' => AddressLibrary::order(['is_deliver_default'=>'desc','id'=>'asc'])->value('id'),
                    'return_address_id' => AddressLibrary::order(['is_return_default'=>'desc','id'=>'asc'])->value('id'),
                    'parcel'        => [
                        [
                            'express_id'    => $express['id'],
                            'invoice_no'    => $info['express_no'],
                            'order_goods_info' => $orderGoodsInfo,
                        ]
                    ],
                    'order_goods_ids' => array_column($orderGoodsInfo,'order_goods_id')
                ];
                if (true !== $validate->scene('delivery')->check($data)) {
                    DeliveryBatch::update([ 'fail' => Db::raw('fail+1') ], [ [ 'id' ,'=', $detail['id'] ] ]);
                    DeliveryBatchInfo::update([ 'status' => 2, 'fail_content' => $validate->getError() ], [ [ 'id', '=', $info['id'] ] ]);
                    continue;
                }
    
                // 发货
                if(! (new OrderLogic)->delivery($data)) {
                    DeliveryBatch::update([ 'fail' => Db::raw('fail+1') ], [ [ 'id' ,'=', $detail['id'] ] ]);
                    DeliveryBatchInfo::update([ 'status' => 2, 'fail_content' => OrderLogic::getError() ], [ [ 'id', '=', $info['id'] ] ]);
                    continue;
                }
            } else if ($order['order_status'] == OrderEnum::STATUS_WAIT_RECEIVE) {
                // 已发货 修改单号
                $data = [
                    'id'                => $order['id'],
                    'express_id'        => $express['id'],
                    'invoice_no'        => $info['express_no'],
                    'delivery_content'  => '',
                ];
                
                $re_result = (new OrderLogic)->changeDelivery($data);
                
                if($re_result !== true) {
                    DeliveryBatch::update([ 'fail' => Db::raw('fail+1') ], [ [ 'id' ,'=', $detail['id'] ] ]);
                    DeliveryBatchInfo::update([ 'status' => 2, 'fail_content' => (string) $re_result ], [ [ 'id', '=', $info['id'] ] ]);
                    continue;
                }
            } else {
                DeliveryBatch::update([ 'fail' => Db::raw('fail+1') ], [ [ 'id' ,'=', $detail['id'] ] ]);
                DeliveryBatchInfo::update([ 'status' => 2, 'fail_content' => '订单不允许操作发货' ], [ [ 'id', '=', $info['id'] ] ]);
                continue;
            }
            
            // 成功
            DeliveryBatch::update([ 'success' => Db::raw('success+1') ], [ [ 'id' ,'=', $detail['id'] ] ]);
            DeliveryBatchInfo::update([ 'status' => 1 ], [ [ 'id', '=', $info['id'] ] ]);
        }
    
        // 更新 已执行导入
        DeliveryBatch::update([ 'status' => 1 ], [ [ 'id' ,'=', $detail['id'] ] ]);
        
        return true;
    }
    
    static function importLists(array $lists, $filename)
    {
        try {
            Db::startTrans();
        
            array_shift($lists);
            $lists  = array_values($lists);
            $nums   = count($lists);
            
            $batch = DeliveryBatch::create([
                'filename'  => $filename,
                'nums'      => $nums,
                'success'   => 0,
                'fail'      => 0,
            ]);
        
            $batchInfoArrays    = [];
        
            foreach ($lists as $info) {
                
                $batchInfoArrays[] = [
                    'batch_id'      => $batch->id,
                    'sn'            => trim($info['A']),
                    'express_name'  => trim($info['B']),
                    'express_no'    => trim($info['C']),
                    'status'        => 0,
                    'fail_content'  => '',
                ];
            }
    
            (new DeliveryBatchInfo())->saveAll($batchInfoArrays);
            
            Db::commit();
            return [ 'id' => $batch->id ];
        } catch(\Throwable $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }
}