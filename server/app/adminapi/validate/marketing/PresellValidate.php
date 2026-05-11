<?php

namespace app\adminapi\validate\marketing;

use app\common\enum\GoodsEnum;
use app\common\enum\PresellEnum;
use app\common\logic\CommonPresellLogic;
use app\common\logic\GoodsActivityLogic;
use app\common\model\Goods;
use app\common\model\GoodsItem;
use app\common\model\Presell;
use app\common\validate\BaseValidate;

class PresellValidate extends BaseValidate
{
    protected $rule = [
        'id'            => [ 'require' ],
        'name'          => [ 'require', 'max' => 50 ],
        'type'          => [ 'require', 'in' => PresellEnum::TYPES ],
        'start_time'    => [ 'require', 'dateFormat:Y-m-d H:i:s' ],
        'end_time'      => [ 'require', 'dateFormat:Y-m-d H:i:s', 'checkEndTime' ],
        'remark'        => [ 'max' => 100 ],
        'goods'         => [ 'array', 'checkGoods' ],
        'send_type'     => [ 'require', 'in' => PresellEnum::SEND_TYPES ],
        'send_type_day' => [ 'require', 'number', 'gt' => 0 ],
        'buy_limit'     => [ 'require', 'in' => [ 0, 1 ], 'checkBuyLimit' ],
    ];
    
    protected $field = [
        'id'            => 'ID',
        'name'          => '活动名称',
        'type'          => '预售类型',
        'start_time'    => '开始时间',
        'end_time'      => '结束时间',
        'remark'        => '备注',
        'goods'         => '商品信息',
        'goods.array'   => '商品信息格式错误',
        'send_type'     => '发货时间',
        'send_type_day' => '发货天数',
        'buy_limit'     => '每单限制',
    ];
    
    protected $message = [
        'type.in'   => '预售类型错误',
        'send_type' => '发货时间错误',
        'buy_limit' => '每单限制错误',
    ];
    
    function sceneAdd()
    {
        return $this->only([
            'name',
            'type',
            'start_time',
            'end_time',
            'remark',
            'goods',
            'send_type',
            'send_type_day',
            'buy_limit',
        ])->append('goods', 'require');
    }
    
    function sceneEdit()
    {
        return $this->only([ 'id' ])->append('id', 'checkIdEdit');
    }
    
    function sceneEditWait()
    {
        return $this->only([
            'name',
            'type',
            'start_time',
            'end_time',
            'remark',
            'goods',
            'send_type',
            'send_type_day',
            'buy_limit',
        ])->append('goods', 'require');
    }
    
    function sceneEditStart()
    {
        return $this->only([
            'name',
            'remark',
        ]);
    }
    
    function sceneDetail()
    {
        return $this->only([ 'id' ]);
    }
    
    function checkEndTime($endTime, $rule, $data)
    {
        if (strtotime($endTime) <= strtotime($data['start_time'])) {
            return '活动结束时间必须大于活动开始时间';
        }
        
        return true;
    }
    
    function checkGoods($goods, $rule, $data)
    {
        if (count($data['goods']) > 25) {
            return '选择的商品不能大于25个';
        }
        
        foreach ($goods as $goodsInfo) {
    
            // 检测商品
            if (empty($goodsInfo)) {
                return '商品不能为空';
            }
            $goodsdDetail = Goods::findOrEmpty($goodsInfo['goods_id'] ?? '');
            if (empty($goodsdDetail['id'])) {
                return '商品不存在';
            }
            if (GoodsEnum::GOODS_VIRTUAL == $goodsdDetail['type']) {
                return '虚拟商品不能参加营销活动';
            }
            if ($this->currentScene == 'add') {
                $goodsActivityInfo = GoodsActivityLogic::goodsActivityInfo($goodsdDetail['id']);
                if ($goodsActivityInfo) {
                    return '商品：' . $goodsdDetail['name'] . ' 正在参与' . $goodsActivityInfo['activity_text'] . '，不能添加';
                }
            }
            if (! empty($goodsInfo['virtual_sale']) && (! is_numeric($goodsInfo['virtual_sale']) || $goodsInfo['virtual_sale'] <= 0)) {
                return '虚拟销量值错误';
            }
            if (! empty($goodsInfo['virtual_click']) && (! is_numeric($goodsInfo['virtual_click']) || $goodsInfo['virtual_click'] <= 0)) {
                return '虚拟浏览量值错误';
            }
            // 检测商品规格
            foreach ($goodsInfo['items'] as $item) {
                if (empty($item['item_id'])) {
                    return '商品规格item_id缺少';
                }
                $goodsItemDetail = GoodsItem::where('goods_id', $goodsdDetail['id'])->findOrEmpty($item['item_id']);
                if (empty($goodsItemDetail['id'])) {
                    return '商品规格不存在';
                }
                if (empty($item['price'])) {
                    return '商品规格预售价格缺少';
                }
                if ($item['price'] >= $goodsItemDetail['sell_price']) {
                    return '商品规格预售价格必须低于售价';
                }
            }
        }
        return true;
    }
    
    function checkBuyLimit($buy_limit, $rule, $data)
    {
        if ($buy_limit == 1) {
            if (! isset($data['buy_limit_num'])) {
                return '限制数量不能为空';
            }
            if (! is_numeric($data['buy_limit_num']) || $data['buy_limit_num'] <= 0) {
                return '限制数量必须为大于0的数字';
            }
        }
        
        return true;
    }
    
    function checkIdEdit($id, $rule, $data)
    {
        $presell = Presell::findOrEmpty($id);
        if (empty($presell['id'])) {
            return '预售活动不存在';
        }
        switch ($presell['status']) {
            case PresellEnum::STATUS_WAIT:
                $validate = new self;
                if (! $validate->scene('editWait')->check($data)) {
                    return $validate->getError();
                }
                break;
            case PresellEnum::STATUS_START:
                $validate = new self;
                if (! $validate->scene('editStart')->check($data)) {
                    return $validate->getError();
                }
                break;
            default:
                return '预售活动当前状态不可编辑';
        }
        
        return true;
    }
}