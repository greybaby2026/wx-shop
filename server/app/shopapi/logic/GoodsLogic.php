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
namespace app\shopapi\logic;
use app\shopapi\logic\Order\FreightLogic;
use app\common\{cache\HandleConcurrencyCache,
    enum\FootprintEnum,
    enum\OrderEnum,
    enum\PayEnum,
    logic\CommonPresellLogic,
    logic\GoodsActivityLogic,
    model\Cart,
    model\Distribution,
    model\DistributionConfig,
    model\DistributionGoods,
    model\DistributionLevel,
    model\FreeShipping,
    model\Goods,
    logic\BaseLogic,
    logic\DiscountLogic,
    model\GoodsItem,
    model\GoodsServiceGuarantee,
    model\GoodsVisit,
    model\GoodsCollect,
    model\OrderGoods,
    model\PresellGoods,
    model\SearchRecord,
    model\GoodsComment,
    enum\GoodsCommentEnum,
    model\User,
    model\UserAddress,
    service\FileService};
use app\common\service\ConfigService;


/**
 * 商品接口逻辑层
 * Class GoodsLogic
 * @package app\shopapi\logic
 */
class GoodsLogic extends BaseLogic
{

    /**
     * @notes 商品详情
     * @param $id
     * @return array|false
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2021/8/2 19:54
     */
    public function detail($params)
    {
        $id = $params['id'];
        $userId = $params['user_id'];

        $goods = Goods::with(['spec_value.spec_list','spec_value_list'])
            ->field('id,name,type,code,image,video,video_cover,total_stock,click_num,virtual_sales_num+sales_num as sales_num,unit_id,spec_type,content,poster,virtual_click_num,express_type,express_money,express_template_id,limit_type,limit_value,status,service_guarantee_ids')
            ->append(['goods_image'])
            ->find($id);
        if(empty($goods)){
             self::$error = '商品已下架！';
             return false;
        }
        
        // 服务保障
        $goods->service_guarantee = GoodsServiceGuarantee::getApiList($goods->service_guarantee_ids);
        
        // 判断是否需要统计浏览量
        if (isset($params['visit'])) {
            //记录点击量
            $goods->click_num = $goods->click_num + 1;
            $goods->save();

            // 浏览量
            $this->visit($id, $userId);
        }

        $stockShow = ConfigService::get('goods_set', 'is_show', 1);
        $showPrice = ConfigService::get('goods_set', 'show_price', 1);
        $goods->is_sales_num = ConfigService::get('goods_set', 'sales_num', 1);
        $goods->is_click_num = ConfigService::get('goods_set', 'click_num', 1);
        $goods->is_show_price = $showPrice;
        $goods->stock_show = true;
        if(0 == $stockShow){
            $goods->stock_show = false;
        }

        $goods->buy_num = 0;//已购买数量
        $goods->cart_goods_num = 0;
        if($userId) {
            // 地址
            $address_id     = input('address_id/d', 0);
            $address        = $address_id ? UserAddress::getAddressById($userId, $address_id) : UserAddress::getDefaultAddress($userId);
            if ($address) {
                $goods->address = $address;
            }
            // 汽泡足迹
            event('Footprint', ['type' => FootprintEnum::BROWSE_PRODUCT, 'user_id' => $userId, 'foreign_id'=>$id]);

            //是否收藏过
            $IsCollect = GoodsCollect::where(['goods_id' => $id, 'user_id' => $userId])->value('id');
            $goods->is_collect = $IsCollect ? 1 : 0;
            //会员价
            $userLevel = User::where(['id' => $userId])
                ->field('id,level')
                ->with('user_level')
                ->findOrEmpty()->toArray();
            $goodsDiscountPrice = DiscountLogic::getGoodsDiscount($userId, [$goods->id])[$goods->id] ?? [];
            foreach ($goods->spec_value_list as $key => $specValue) {
                $specValue['member_price']      = $goodsDiscountPrice[$specValue['id']]['discount_price'] ?? '';
                $specValue['member_level']      = [
                    'name'  => $userLevel['name'] ?? '',
                ];
            }
            $goods->member_price    = $goods->spec_value_list[0]->member_price;
            $goods->member_level    = [
                'name'  => $userLevel['name'] ?? '',
            ];

            //用户下单数量
            $goods->buy_num = OrderGoods::alias('og')
                ->join('order o', 'o.id = og.order_id')
                ->where(['og.goods_id'=>$id,'o.order_status'=>[OrderEnum::STATUS_WAIT_PAY,OrderEnum::STATUS_WAIT_DELIVERY,OrderEnum::STATUS_WAIT_RECEIVE,OrderEnum::STATUS_FINISH],'o.user_id'=>$params['user_id'],'o.order_type'=>[OrderEnum::NORMAL_ORDER,OrderEnum::VIRTUAL_ORDER]])
                ->sum('og.goods_num');

            //购物车商品数量
            $goods->cart_goods_num = Cart::where(['goods_id'=>$id,'user_id'=>$params['user_id']])->sum('goods_num');
        }
        if(0 == $showPrice){
            foreach ($goods->spec_value_list as $key =>$specValue){
                $specValue['lineation_price'] = 0;
            }
        }
        $goods->sell_price      = $goods->spec_value_list[0]->sell_price;
        $goods->lineation_price = $goods->spec_value_list[0]->lineation_price;

        $goods->goods_comment = $this->getComment($goods->id);

        $goods->click_num += $goods->virtual_click_num;
        $goods->unit_name = '';
        if($goods->unit_id){
            $goods->unit_name = $goods->unit->name;
        }
        $goods->hidden(['unit_id','unit']);

        // 预估佣金
        $goods->distribution = self::getDistribution($id, $userId);

        // 包邮信息
        $goods->free_shipping_tips = self::getFreeShippingTips($userId, $goods);
        
        // 预售信息
        $goods = CommonPresellLogic::goodsDetailInfo($goods);


        return $goods->toArray();
    }

    /**
     * @notes 商品搜索记录
     * @param $userId
     * @param $limit
     * @return array
     * @author cjhao
     * @date 2021/8/11 17:12
     */
    public function searchRecord($userId,$limit){
        //读取缓存
        $HandleConcurrencyCache = new HandleConcurrencyCache();
        $recordList = $HandleConcurrencyCache->getCache($HandleConcurrencyCache->getGoodsSearchRecordKey($userId));
        if ($recordList !== false) {
            return $recordList;
        }

        $recordList = SearchRecord::where(['user_id'=>$userId])
            ->limit($limit)
            ->order('id desc')
            ->column('keyword');

        //设置缓存
        $HandleConcurrencyCache->setCache($HandleConcurrencyCache->getGoodsSearchRecordKey($userId),$recordList,3600);

        return $recordList;
    }

    /**
     * @notes 商品营销接口
     * @param int $goodsId
     * @param int $userId
     * @return array
     * @author cjhao
     * @date 2021/8/27 17:27
     */
    public function goodsMarketing(int $goodsId,int $userId):array
    {
        $coupon = CouponLogic::goodsCoupon($goodsId,$userId);
        $activityList = GoodsActivityLogic::activityInfo($goodsId)[$goodsId] ?? [];
        $marketing = [
            'coupon'    => $coupon,
            'activity'  => array_values($activityList),
        ];

        return $marketing;

    }

    /**
     * @notes 清空搜索记录
     * @param int $userId
     * @author cjhao
     * @date 2021/9/15 11:35
     */
    public function clearRecord(int $userId)
    {
        //删除缓存
        $HandleConcurrencyCache = new HandleConcurrencyCache();
        $HandleConcurrencyCache->deleteCache($HandleConcurrencyCache->getGoodsSearchRecordKey($userId));

        SearchRecord::where(['user_id'=>$userId])->delete();
    }

    /**
     * @notes 商品浏览记录
     * @param $goodsId
     * @param $userId
     * @return bool
     * @author Tab
     * @date 2021/9/15 14:04
     */
    public function visit($goodsId, $userId)
    {
        if (empty($userId)) {
            $userId = 0;
        }
        $ip = request()->ip();

        // 一个ip一个商品一个用户一天只生成一条记录
        $record = GoodsVisit::where([
            'ip' => $ip,
            'goods_id' => $goodsId,
            'user_id' => $userId,
        ])->whereDay('create_time')->findOrEmpty();

        if (!$record->isEmpty()) {
            // 增加浏览量
            $record->visit += 1;
            $record->save();
            return true;
        }

        // 生成商品浏览记录
        GoodsVisit::create([
            'ip' => $ip,
            'goods_id' => $goodsId,
            'user_id' => $userId,
            'visit' => 1
        ]);
    }

    /**
     * @notes 获取最近的商品评价
     * @param $id
     * @param int $limit
     * @author cjhao
     * @date 2021/11/17 17:44
     */
    public static function getComment($id,$limit = 1){
        //商品评论
        $goodsComment = GoodsComment::with(['goods_comment_image','user'])
            ->where(['goods_id'=>$id,'status'=>GoodsCommentEnum::APPROVED])
            ->field('id,user_id,spec_value_str,comment,virtual')
            ->order('id desc')
            ->limit($limit)
            ->findOrEmpty();

        if(!$goodsComment->isEmpty()){
            $commentCount = GoodsComment::where(['goods_id'=>$id,'status'=>GoodsCommentEnum::APPROVED])->count();
            $goodsCommentCount = GoodsComment::where([['goods_id','=',$id],['goods_comment','>',3],['status', '=', GoodsCommentEnum::APPROVED]])->count();

            $goodsRate = $commentCount > 0 ? round(($goodsCommentCount/$commentCount)*100).'%' : '100%';
            $goodsComment->goods_rate = $goodsRate;
            $goodsComment->comment_image = array_column($goodsComment->goods_comment_image->toArray(),'uri');
            $goodsComment->hidden(['user_id','goods_comment_image']);

            if (!is_null($goodsComment->virtual)) {
                // 虚拟评价
                $vitual = json_decode($goodsComment->virtual, true);
                $goodsComment->nickname = $vitual['nickname'];
                $goodsComment->avatar = FileService::getFileUrl($vitual['avatar']);
            }
            //隐藏用户昵称
            $goodsComment->nickname = hide_substr($goodsComment->nickname);
            if(empty($goodsComment->comment)){
                $goodsComment->comment = '此用户没有填写评论';
            }
        }
        return $goodsComment->toArray();
    }

    /**
     * 计算最高可得预估佣金
     */
    public static function getDistribution($goodsId, $userId)
    {
        $earnings       = 0;
        $ratio          = 0;
        $rule           = 0;
        
        $goods = Goods::findOrEmpty($goodsId)->toArray();
        // 用户分销等级
        $distribution_level_id  = Distribution::where('user_id', $userId)->where('is_distribution', 1)->value('level_id', 0);
        $distribution_level     = DistributionLevel::where('id', $distribution_level_id)->findOrEmpty()->toArray();
        // 分销等级
        $default_level          = DistributionLevel::where('is_default', 1)->findOrEmpty()->toArray();
        $level                  = $distribution_level ? : $default_level;
        // 分销商品
        $distributionGoods      = DistributionGoods::where('goods_id', $goodsId)->where('is_distribution', 1)->select()->toArray();
        $rule                   = $distributionGoods[0]['rule'] ?? $rule;
        
        // 按分销等级比例分佣
        if ($rule == 1) {
            $ratio      = $level['first_ratio'] ?? 0;
            $earnings   = bcdiv(($goods['max_price'] ?? 0) * $ratio , 100, 2);
        }
        // 单独设置分佣比例
        if ($rule == 2) {
            foreach ($distributionGoods as $distributionGood) {
                if ($distributionGood['level_id'] == $level['id']) {
                    $ratio      = $distributionGood['first_ratio'] ?? 0;
                    $earnings   = bcdiv(($goods['max_price'] ?? 0) * $ratio , 100, 2);
                    break;
                }
            }
        }
        
        $dbConfig = DistributionConfig::column('value', 'key');
        // 分销总开关
        $switch = empty($dbConfig['switch']) ? 0 : 1;
        // 商品详情页是否显示佣金 0-不显示 1-显示
        if (!isset($dbConfig['is_show_earnings'])) {
            $isShowEarnings = 1;
        } else if(empty((int)$dbConfig['is_show_earnings'])) {
            $isShowEarnings = 0;
        } else {
            $isShowEarnings = 1;
        }
        // 详情页佣金可见用户 0-全部用户 1-分销商
        if (!isset($dbConfig['show_earnings_scope'])) {
            $showEarningsScope = 0;
        } else if(empty((int)$dbConfig['show_earnings_scope'])) {
            $showEarningsScope = 0;
        } else {
            $showEarningsScope = 1;
        }
        if (!$switch || empty($distributionGoods)) {
            $isShowEarnings = 0;
        }
        if ($isShowEarnings) {
            $user = Distribution::where(['user_id' => $userId])->findOrEmpty()->toArray();
            if ($showEarningsScope && empty($user['is_distribution'])) {
                $isShowEarnings = 0;
            }
        }

        return [
            'is_show'   => $isShowEarnings,
            'earnings'  => $earnings,
            'ratio'     => $ratio,
            'rule'      => $rule,
        ];
    }

    /**
     * @notes 自定义海报获取商品信息
     */
    public static function getGoodsByTypeId($type, $activityId, $goodsId, $userId) {
        // type 1普通通商品 2秒杀  3拼团 4砍价 5预售
        switch($type) {
            case 1:
                return Goods::field('name, image, min_lineation_price, min_price')->where('id', $goodsId)->findOrEmpty()->toArray();
            case 2:
                $data = SeckillLogic::detail([
                    'id' => $activityId,
                    'user_id' => $userId,
                ]);
                if (!is_array($data)) {
                    return [];
                }
                return [
                    'name' => $data['name'],
                    'image' => $data['image'],
                    'min_lineation_price' => $data['min_price'],
                    'min_price' => $data['activity']['min_seckill_price'],
                ];
            case 3:
                $data = TeamLogic::detail($activityId, $userId);
                if (!is_array($data)) {
                    return [];
                }
                return [
                    'name' => $data['name'],
                    'image' => $data['image'],
                    'min_lineation_price' => $data['min_price'],
                    'min_price' => $data['activity']['min_team_price'],
                ];
            case 4:
                $data = BargainLogic::detail([
                    'activity_id' => $activityId,
                    'goods_id' => $goodsId,
                ]);
                if (!is_array($data)) {
                    return [];
                }
                return [
                    'name' => $data['goods_name'],
                    'image' => $data['image'],
                    'min_lineation_price' => $data['goods_max_price'],
                    'min_price' => $data['min_price'],
                ];
            case 5:
                $data = PresellGoods::where(['id'=>$goodsId])->findOrEmpty()->toArray();
                if (empty($data)) {
                    return [];
                }
                $goods = Goods::where(['id'=>$data['goods_id']])->field('name,image')->findOrEmpty()->toArray();

                return [
                    'name' => $goods['name'],
                    'image' => $goods['image'],
                    'min_lineation_price' => '',
                    'min_price' => $data['min_price'],
                ];
            default:
                return [];
        }
    }

    public static function getFreeShippingTips($userId, $goods)
    {
        $expressTips    = '';
        $activityTips   = '';
        $address        = $goods['address'] ?? [];
        
        // 用户未登录
        if (empty($userId) || empty($address['id'])) {
            return '免运费';
        }
        
        // 包邮活动
        $activity = FreeShipping::where([
            ['start_time', '<=', time()],
            ['end_time', '>', time()],
            ['status', '=', 1]
        ])->findOrEmpty()->toArray();
        
        if ($activity) {
            foreach($activity['region'] as $item) {
                if ($item->region_id == "100000") {
                    // 全国区域
                    if ($activity['condition_type'] == 1) {
                        $activityTips = '订单满' . $item->threshold . '元包邮';
                    } else {
                        $activityTips = '订单满' . $item->threshold . '件包邮';
                    }
                    continue;
                }
                
                // 未设置地址
                if(empty($address)) {
                    continue;
                }
        
                if (
                    str_contains($item->region_id, $address['district_id'])
                    ||
                    str_contains($item->region_id, $address['city_id'])
                    ||
                    str_contains($item->region_id, $address['province_id'])
                ) {
                    if ($activity['condition_type'] == 1) {
                        $activityTips = '订单满' . $item->threshold . '元包邮';
                    } else {
                        $activityTips = '订单满' . (int)$item->threshold . '件包邮';
                    }
                    break;
                }
            }
            
        }
        
        switch ($goods['express_type']) {
            // 包邮
            case 1:
                $expressTips    = '免运费';
                $activityTips   = '';
                break;
            // 统一运费
            case 2:
                if ($goods['express_money'] > 0) {
                    $expressTips    = "￥{$goods['express_money']}";
                } else {
                    $expressTips    = '免运费';
                    $activityTips   = '';
                }
                break;
            // 运费模板
            case 3:
                $express = FreightLogic::calculateFreight([
                    [
                        'weight'                => $goods['spec_value_list'][0]['weight'] ?? 0,
                        'volume'                => $goods['spec_value_list'][0]['volume'] ?? 0,
                        'goods_num'             => 1,
                        'id'                    => $goods['id'],
                        'express_type'          => $goods['express_type'],
                        'express_money'         => $goods['express_money'],
                        'express_template_id'   => $goods['express_template_id'],
                    ]
                ], $address);
                
                $express_money  = $express['express_price'] ?? 0;
                
                if ($express_money > 0) {
                    $expressTips    =  "￥{$express_money}";
                } else {
                    $expressTips    = '免运费';
                    $activityTips   = '';
                }
                break;
            default:
                $expressTips    = '免邮';
                $activityTips   = '';
                break;
        }
        
        return "{$expressTips} {$activityTips}";
    }

    static function checkCanBuy($item_id, $num) : bool|string
    {
        $item    = GoodsItem::findOrEmpty($item_id);
        
        $goods  = Goods::findOrEmpty($item['goods_id'] ?? 0);
        
        if (empty($goods['id'])) {
            return '找不到商品';
        }
        
        if ($goods['status'] == 0) {
            return '商品不能购买';
        }
        
        if ($item['stock'] < $num) {
            return '商品库存不足';
        }
        
        return true;
    }
}
