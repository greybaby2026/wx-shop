<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------

namespace app\adminapi\logic\marketing;

use app\common\enum\ActivityEnum;
use app\common\enum\PresellEnum;
use app\common\logic\BaseLogic;
use app\common\model\Goods;
use app\common\model\GoodsActivity;
use app\common\model\GoodsItem;
use app\common\model\Presell;
use app\common\model\PresellGoods;
use app\common\model\PresellGoodsItem;
use think\facade\Db;

class PresellLogic extends BaseLogic
{
    static function add($params)
    {
        try {
            Db::startTrans();
            
            $presellData = [
                'name'          => $params['name'],
                'type'          => $params['type'],
                'start_time'    => $params['start_time'],
                'end_time'      => $params['end_time'],
                'remark'        => $params['remark'] ?? '',
                'send_type'     => $params['send_type'],
                'send_type_day' => $params['send_type_day'],
                'buy_limit'     => $params['buy_limit'],
                'buy_limit_num' => $params['buy_limit'] == 1 ? $params['buy_limit_num'] : 0,
            ];
            
            $presell = Presell::create($presellData);
    
            static::createGoods($params, $presell);
            
            Db::commit();
            return true;
        } catch(\Throwable $e) {
            static::setError($e->getMessage());
            Db::rollback();
            return false;
        }
    }
    
    static function edit($params)
    {
        try {
            Db::startTrans();
            
            $presell = Presell::findOrEmpty($params['id']);
            
            // 活动未开始 可以编辑全部
            if ($presell['status'] == PresellEnum::STATUS_WAIT) {
                // 清除相关信息
                PresellGoods::destroy(function ($query) use ($params) {
                    $query->where('presell_id', $params['id']);
                });
                PresellGoodsItem::destroy(function ($query) use ($params) {
                    $query->where('presell_id', $params['id']);
                });
                GoodsActivity::destroy(function ($query) use ($params) {
                    $query->where('activity_id', $params['id'])->where('activity_type', ActivityEnum::PRESELL);
                });
    
                Presell::update([
                    'id'            => $params['id'],
                    'name'          => $params['name'],
                    'type'          => $params['type'],
                    'start_time'    => $params['start_time'],
                    'end_time'      => $params['end_time'],
                    'remark'        => $params['remark'] ?? '',
                    'send_type'     => $params['send_type'],
                    'send_type_day' => $params['send_type_day'],
                    'buy_limit'     => $params['buy_limit'],
                    'buy_limit_num' => $params['buy_limit'] == 1 ? $params['buy_limit_num'] : 0,
                ]);
                
                static::createGoods($params, $presell);
            }
            
            // 活动已开始 只能编辑部分信息
            if ($presell['status'] == PresellEnum::STATUS_START) {
                $presellData = [
                    'id'            => $params['id'],
                    'name'          => $params['name'],
                    'remark'        => $params['remark'] ?? '',
                ];
    
                Presell::update($presellData);
            }
            
            Db::commit();
            return true;
        } catch(\Throwable $e) {
            static::setError($e->getMessage());
            Db::rollback();
            return false;
        }
    }
    
    static function createGoods($params, $presell)
    {
        foreach ($params['goods'] ?? [] as $goods) {
            // 预售商品
            $goodsDetail = Goods::findOrEmpty($goods['goods_id'])->toArray();
            if (empty($goodsDetail['id'])) {
                throw new \Exception('商品不存在');
            }
            $presellGoods = PresellGoods::create([
                'presell_id'    => $presell->id,
                'goods_id'      => $goods['goods_id'],
                'min_price'     => min(array_column($goods['items'], 'price')),
                'max_price'     => max(array_column($goods['items'], 'price')),
                'virtual_sale'  => $goods['virtual_sale'] ?? 0,
                'virtual_click' => $goods['virtual_click'] ?? 0,
                'content'       => $goodsDetail,
            ]);
            // 预售规格
            foreach ($goods['items'] as $item) {
                $itemInfo = GoodsItem::where('goods_id', $goods['goods_id'])->findOrEmpty($item['item_id'])->toArray();
                if (empty($itemInfo['id'])) {
                    throw new \Exception('商品规格不存在');
                }
                PresellGoodsItem::create([
                    'presell_id'        => $presell->id,
                    'presell_goods_id'  => $presellGoods->id,
                    'goods_id'          => $goods['goods_id'],
                    'item_id'           => $item['item_id'],
                    'price'             => $item['price'],
                    'content'           => $itemInfo,
                ]);
                // 添加活动信息
                GoodsActivity::create([
                    'activity_type' => ActivityEnum::PRESELL,
                    'activity_id'   => $presell['id'],
                    'goods_id'      => $goods['goods_id'],
                    'item_id'       => $item['item_id'],
                ]);
            }
        }
    }
    
    static function detail($params)
    {
        $detail = Presell::where('id', $params['id'] ?? '')
            ->with([
                'goods' => function($query) {
                    $query->with([ 'item' ]);
                }
            ])
            ->findOrEmpty()
            ->toArray();
        
        foreach ($detail['goods'] ?? [] as $key =>  $goods) {
            $goodsDetail = Goods::field('total_stock,spec_type,min_price')->findOrEmpty($goods['goods_id']);
            $detail['goods'][$key]['id']            = $goods['goods_id'];
            $detail['goods'][$key]['name']          = $goods['content']['name'];
            $detail['goods'][$key]['image']         = $goods['content']['image'];
            $detail['goods'][$key]['total_stock']   = $goodsDetail['total_stock'];
            $detail['goods'][$key]['spec_type']     = $goods['content']['spec_type'];
            $detail['goods'][$key]['sell_price']    = $goods['content']['min_price'];
            
            unset($detail['goods'][$key]['content']);
            
            foreach ($goods['item'] as $ko => $item) {
                $detail['goods'][$key]['item'][$ko]['id']             = $item['item_id'];
                $detail['goods'][$key]['item'][$ko]['sell_price']     = $item['content']['sell_price'];
                $detail['goods'][$key]['item'][$ko]['spec_value_str'] = $item['content']['spec_value_str'];
                $detail['goods'][$key]['item'][$ko]['stock']          = GoodsItem::where('id', $item['item_id'])->value('stock');
    
                unset($detail['goods'][$key]['item'][$ko]['content']);
            }
        }
        
        return $detail;
    }
    
    static function start($params)
    {
        try {
            Db::startTrans();
            
            $presell = Presell::findOrEmpty($params['id']);
            
            if (empty($presell)) {
                throw new \Exception('预售活动不存在');
            }
            
            if ($presell->status != PresellEnum::STATUS_WAIT) {
                throw new \Exception('当前状态不能开启活动');
            }
    
            Presell::update([
                'id'        => $presell->id,
                'status'    => PresellEnum::STATUS_START,
            ]);
            
            Db::commit();
            return true;
        } catch(\Throwable $e) {
            static::setError($e->getMessage());
            Db::rollback();
            return false;
        }
    }
    
    static function end($params)
    {
        try {
            Db::startTrans();
            
            $presell = Presell::findOrEmpty($params['id']);
            
            if (empty($presell)) {
                throw new \Exception('预售活动不存在');
            }
            
            if ($presell->status != PresellEnum::STATUS_START) {
                throw new \Exception('当前状态不能结束活动');
            }
            
            Presell::update([
                'id'        => $presell->id,
                'status'    => PresellEnum::STATUS_END,
            ]);
    
            // 清除相关信息
            GoodsActivity::destroy(function ($query) use ($params) {
                $query->where('activity_id', $params['id'])->where('activity_type', ActivityEnum::PRESELL);
            });
            
            Db::commit();
            return true;
        } catch(\Throwable $e) {
            static::setError($e->getMessage());
            Db::rollback();
            return false;
        }
    }
    
    static function delete($params)
    {
        try {
            Db::startTrans();
            
            $presell = Presell::findOrEmpty($params['id']);
            
            if (empty($presell)) {
                throw new \Exception('预售活动不存在');
            }
            
            Presell::destroy($presell->id);
    
            // 清除相关信息
            PresellGoods::destroy(function ($query) use ($params) {
                $query->where('presell_id', $params['id']);
            });
            PresellGoodsItem::destroy(function ($query) use ($params) {
                $query->where('presell_id', $params['id']);
            });
            GoodsActivity::destroy(function ($query) use ($params) {
                $query->where('activity_id', $params['id'])->where('activity_type', ActivityEnum::PRESELL);
            });
            
            Db::commit();
            return true;
        } catch(\Throwable $e) {
            static::setError($e->getMessage());
            Db::rollback();
            return false;
        }
    }
}