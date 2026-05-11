<?php

namespace app\common\model\search;

use app\common\enum\GoodsEnum;
use app\common\enum\PresellEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\GoodsActivityLogic;
use app\common\model\DiscountGoods;
use app\common\model\DistributionGoods;
use app\common\model\GoodsBrand;
use app\common\model\GoodsCategory;
use app\common\model\GoodsCategoryIndex;
use app\common\model\GoodsItem;
use app\common\model\PresellGoods;
use think\facade\Validate;

trait SearchGoods
{
    /**
     * @notes 关键词搜索
     * @param $query
     * @param $value
     * @param $data
     * @author cjhao
     * @date 2021/8/16 18:01
     */
    public function searchKeywordAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('code|name', 'like', '%' . $value . '%');
        }
    }
    
    /**
     * @notes 搜索条形码
     * @param $query
     * @param $value
     * @param $data
     * @return void
     * @author lbzy
     * @datetime 2023-12-21 11:49:39
     */
    function searchBarCodeAttr($query, $value, $data)
    {
        Validate::must($value) && $query->where('id', 'in', GoodsItem::where('bar_code', $value)->column('goods_id'));
    }
    
    /**
     * @notes 分类搜索
     * @param $query
     * @param $value
     * @param $data
     * @author cjhao
     * @date 2021/8/16 18:02
     */
    public function searchCategoryIdAttr($query, $value, $data)
    {
        if ($value) {
            $goodsCategory = GoodsCategory::find($value);
            $level = $goodsCategory['level'] ?? '';
            $categoryIds = [];
            switch ($level) {
                case 1:
                    $oneCategoryIds = GoodsCategory::where(['pid' => $value])
                        ->column('id');
                    $twoCategoryIds = [];
                    foreach ($oneCategoryIds as $categoryId) {
                        $twoCategoryIds = array_merge($twoCategoryIds, GoodsCategory::where(['pid' => $categoryId])->column('id'));
                    }
                    $categoryIds = array_merge($oneCategoryIds, $twoCategoryIds);
                    break;
                case 2:
                    $categoryIds = GoodsCategory::where(['pid' => $value])
                        ->column('id');
                    break;
            }
            $categoryIds = array_merge([(int)$value], $categoryIds);
            $goodsIds = GoodsCategoryIndex::where(['category_id' => $categoryIds])->column('goods_id');
            $query->where('id', 'in', $goodsIds);
            
        }
        
    }
    
    /**
     * @notes 品牌搜索
     * @param $query
     * @param $value
     * @param $data
     * @return void
     * @author lbzy
     * @datetime 2023-11-08 09:49:20
     */
    public function searchBrandIdAttr($query, $value, $data)
    {
        $value && $query->where('brand_id', 'in', $value);
    }
    
    /**
     * @notes 商品类型筛选
     * @param $query
     * @param $value
     * @param $data
     * @author cjhao
     * @date 2022/4/21 18:05
     */
    public function searchGoodsTypeAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('type', '=', $value);
        }
    }
    
    /**
     * @notes 供应商搜索
     * @param $query
     * @param $value
     * @param $data
     * @author cjhao
     * @date 2021/8/16 18:02
     */
    public function searchSupplierIdAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('supplier_id', '=', $value);
        }
        
    }
    
    /**
     * @notes 价格排序
     * @param $query
     * @param $value
     * @param $data
     * @author cjhao
     * @date 2021/8/16 18:02
     */
    public function searchPriceAttr($query, $value, $data)
    {
        if ($value) {
            $query->order(['min_price' => $value]);
        }
    }
    
    /**
     * @notes 销售排序
     * @param $query
     * @param $value
     * @param $data
     * @author cjhao
     * @date 2021/8/16 18:02
     */
    public function searchSaleAttr($query, $value, $data)
    {
        if ($value) {
            $query->order(['sales_num' => $value]);
        }
    }
    
    /**
     * @notes 商品名称搜索
     * @param $query
     * @param $value
     * @param $data
     * @author cjhao
     * @date 2021/8/16 18:02
     */
    public function searchNameAttr($query, $value, $data)
    {
        if (Validate::must($value)) {
            $brandId = GoodsBrand::where('name', $value)->where('is_show', 1)->value('id');
            if ($brandId) {
                $query->where('brand_id', $brandId);
            } else {
                $query->where('name', 'like', '%' . $value . '%');
            }
        }
    }
    
    /**
     * @notes 类型搜索
     * @param $query
     * @param $value
     * @param $data
     * @author cjhao
     * @date 2021/8/16 18:02
     */
    public function searchTypeAttr($query, $value, $data)
    {
        switch ($value) {
            case 1:   //销售中 todo 列出「销售状态：销售中」的商品
                $query->where(['status' => GoodsEnum::STATUS_SELL]);
                break;
            case 2:  //库存预警 todo 列出「销售状态：销售中」且「库存预警：开启预警」 且「0 < 总库存 < 库存预警值」
                $query->where([
                    ['status', '=', GoodsEnum::STATUS_SELL],
                    ['stock_warning', '>', 0],
                    ['total_stock', '>', 0],
                ]);
                $query->whereColumn('stock_warning', '>', 'total_stock');
                break;
            
            case 3:  //已售罄 todo 「销售状态：销售中」且 「总库存 == 0」
                $query->where(['status' => GoodsEnum::STATUS_SELL, 'total_stock' => 0]);
                break;
            case 4:  //仓库中 todo 列出「销售状态：仓库中」的商品
                $query->where(['status' => GoodsEnum::STATUS_STORAGE]);
                break;
            
        }
        
    }
    
    /**
     * @notes 根据活动状态搜索
     * @param $query
     * @param $value
     * @param $data
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2022/9/1 17:10
     */
    public function searchActivityTypeAttr($query, $value, $data)
    {
        //全部
        if('' == $value){
            return false;
        }
        //未参与
        if(0 == $value){
            
            $activityLists = GoodsActivityLogic::activityInfo();
            $goodsIds = array_keys($activityLists);
            return $query->where('id', 'not in', $goodsIds);
            
        }else{
            //参与某个活动
            $activityLists = GoodsActivityLogic::activityInfo([],$value);
            $goodsIds = array_keys($activityLists);
            return $query->where('id', 'in', $goodsIds);
        }
        return false;
    }
    
    /**
     * @notes 根据分销状态搜索
     * @param $query
     * @param $value
     * @param $data
     * @author Tab
     * @date 2021/7/23 16:01
     */
    public function searchIsDistributionAttr($query, $value, $data)
    {
        // 获取已开启分销的商品id
        $joinIds = DistributionGoods::where('is_distribution', YesNoEnum::YES)->column('goods_id');
        
        if ($value == 1) {
            // 分销商品
            return $query->where('id', 'in', $joinIds);
        }
        if ($value == 0) {
            // 非分销商品
            return $query->where('id', 'not in', $joinIds);
        }
        return false;
    }
    
    /**
     * @notes 是否折扣商品
     * @param $query
     * @param $value
     * @param $data
     * @return mixed
     * @author cjhao
     * @date 2022/5/5 15:31
     */
    public function searchDiscountAttr($query, $value, $data)
    {
        if ($value) {
            $query->where('is_discount', '=', $value);
        }
        if (0 == $value) {
            $query->where('is_discount = 0 or is_discount is null');
        }
    }
    
    /**
     * @notes 状态筛选
     * @param $query
     * @param $value
     * @param $data
     * @author cjhao
     * @date 2022/5/5 15:38
     */
    public function searchStatusAttr($query, $value, $data)
    {
        if ('' !== $value) {
            $query->where('status', '=', $value);
        }
    }
    
    /**
     * @notes 搜索最大价格 max_price
     * @param $query
     * @param $value
     * @param $data
     * @return void
     * @author lbzy
     * @datetime 2023-11-08 09:51:26
     */
    function searchMaxPriceAttr($query, $value, $data)
    {
        Validate::must($value) && $query->where('max_price', '<=', $value);
    }
    
    /**
     * @notes 搜索最小价格 min_price
     * @param $query
     * @param $value
     * @param $data
     * @return void
     * @author lbzy
     * @datetime 2023-11-08 09:52:16
     */
    function searchMinPriceAttr($query, $value, $data)
    {
        Validate::must($value) && $query->where('min_price', '>=', $value);
    }
    
    /**
     * @notes 搜搜是否会员价 is_member_price 0或1
     * @param $query
     * @param $value
     * @param $data
     * @return void
     * @author lbzy
     * @datetime 2023-11-08 09:58:32
     */
    function searchIsMemberPriceAttr($query, $value, $data)
    {
        if (Validate::must($value)) {
            $discountGoodsIds = DiscountGoods::where('is_discount', 1)->column('goods_id');
            if ($value == 1) {
                $query->whereIn('id', $discountGoodsIds);
            } else {
                $query->whereNotIn('id', $discountGoodsIds);
            }
        }
    }
    
    /**
     * @notes 搜索预售 presell
     * @param $query
     * @param $value
     * @param $data
     * @return void
     * @author lbzy
     * @datetime 2024-05-07 09:35:15
     */
    function searchPresellAttr($query, $value, $data)
    {
        if (Validate::must($value)) {
            $goodsIds = PresellGoods::alias('pg')
                ->join('presell p', 'pg.presell_id=p.id')
                ->where('p.status', 'in' , PresellEnum::STATUS_GOODS_HAS)
                ->column('pg.goods_id');
            if ($value == 0) {
                $query->whereNotIn('id', $goodsIds);
            }
            if ($value == 1) {
                $query->whereIn('id', $goodsIds);
            }
        }
    }
}