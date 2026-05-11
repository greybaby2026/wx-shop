<?php

namespace app\common\logic;

use app\common\enum\PresellEnum;
use app\common\model\Presell;
use app\common\model\PresellGoods;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;

class CommonPresellLogic extends BaseLogic
{
    /**
     * @notes 检查商品是否在参与预售
     * @param $goods_id
     * @return bool
     * @author lbzy
     * @datetime 2024-04-25 09:21:14
     */
    static function checkGoodsHas($goods_id) : bool
    {
        $presell = Presell::alias('p')
            ->join('presell_goods pg', 'p.id=pg.presell_id')
            ->where('p.status', 'in', PresellEnum::STATUS_GOODS_HAS)
            ->where('pg.goods_id', $goods_id)
            ->field('p.id')
            ->findOrEmpty()->toArray();
        
        return ! empty($presell);
    }
    
    /**
     * @notes 获取商品详情 预售信息
     * @param $goods
     * @return object
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws DataNotFoundException
     * @author lbzy
     * @datetime 2024-04-26 10:42:29
     */
    static function goodsDetailInfo($goods)
    {
        $goods_id = $goods->id;
        
        $id = PresellGoods::alias('pg')
            ->join('presell p', 'pg.presell_id=p.id')
            ->where('p.status', PresellEnum::STATUS_START)
            ->where('p.start_time', '<=', time())
            ->where('p.end_time', '>=', time())
            ->where('pg.goods_id', $goods_id)->value('p.id');
        
        PresellGoods::update([ 'click' => Db::raw('click+1') ], [
            [ 'goods_id'  ,'=', $goods_id ],
            [ 'presell_id', '=', $id ],
        ]);
        
        $detail = $id ? Presell::with([
                'goods_detail' => function ($query) use ($goods_id) {
                    $query->hidden([ 'content' ])
                        ->where('goods_id', $goods_id)
                        ->field([ 'id', 'goods_id', 'presell_id', 'click', 'virtual_click', 'virtual_sale', 'max_price', 'min_price' ])
                        ->with([
                            'items' => function ($query) {
                                $query->hidden([ 'content' ])
                                    ->field([ 'id', 'item_id', 'presell_id', 'presell_goods_id', 'price', 'goods_id', 'sale_nums' ]);
                            }
                        ]);
                }
            ])
            ->field([
                'id',
                'name',
                'type',
                'start_time',
                'end_time',
                'remark',
                'send_type',
                'send_type_day',
                'buy_limit',
                'buy_limit_num',
                'status',
                'end_time as end_time_origin'
            ])
            ->append([ 'status_text', 'type_text', 'send_type_text', 'end_time_remain' ])
            ->findOrEmpty($id)
            ->toArray() : null;
        if (!empty($detail)) {
            $detail['goods_detail']['sale_nums'] = array_sum(array_column($detail['goods_detail']['items'], 'sale_nums'));
        }
        $goods->presell = !empty($detail) ? $detail : null;
        
        if ($goods->presell) {
            $presell_items = array_column($goods->presell['goods_detail']['items'], null, 'item_id');
            foreach ($goods->spec_value_list as $key => $spec_value) {
                $goods->spec_value_list[$key]['presell_price'] = $presell_items[$spec_value['id']]['price'] ?? null;
            }
        }
        
        return $goods;
    }
    
    /**
     * @notes 订单详情
     * @param $order
     * @return array|Presell|\think\Model
     * @author lbzy
     * @datetime 2024-04-26 17:36:54
     */
    static function orderInfo($order)
    {
        $detail = Presell::withTrashed()
            ->where('id', $order['presell_id'])
            ->field([
                'id',
                'name',
                'type',
                'start_time',
                'end_time',
                'remark',
                'send_type',
                'send_type_day',
                'buy_limit',
                'buy_limit_num',
                'status',
            ])
            ->append([ 'status_text', 'type_text', 'send_type_text' ])
            ->findOrEmpty()
            ->toArray();
        
        if ($detail) {
            $detail['order_send_text'] = '';
            if ($detail['send_type'] == PresellEnum::SEND_TYPE_PAY_SUCCESS) {
                if ($order['pay_status'] > 0) {
                    $detail['order_send_text'] = date('Y-m-d H:i:s', strtotime($order['pay_time']) + $detail['send_type_day'] * 24 * 3600) . '前发货';
                } else {
                    $detail['order_send_text'] = '等待支付完成';
                }
            }
    
            if ($detail['send_type'] == PresellEnum::SEND_TYPE_END) {
                $detail['order_send_text'] = date('Y-m-d H:i:s', strtotime($detail['end_time']) + $detail['send_type_day'] * 24 * 3600) . '前发货';
            }
        }
        
        return $detail;
    }
}