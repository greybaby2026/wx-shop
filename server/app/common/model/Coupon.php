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

namespace app\common\model;

use app\common\enum\CouponEnum;
use app\common\service\TimeService;
use think\model\concern\SoftDelete;

class Coupon extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';
    
    function getUseGoodsIdsAttr($fieldValue, $data)
    {
        return $fieldValue ? explode(',', $fieldValue) : [];
    }
    
    function setUseGoodsIdsAttr($fieldValue, $data)
    {
        return is_array($fieldValue) ? implode(',', $fieldValue) : ((string) $fieldValue);
    }
    
    function getUseGoodsCategoryIdsAttr($fieldValue, $data)
    {
        return $fieldValue ? explode(',', $fieldValue) : [];
    }
    
    function setUseGoodsCategoryIdsAttr($fieldValue, $data)
    {
        return is_array($fieldValue) ? implode(',', $fieldValue) : ((string) $fieldValue);
    }
    
    function getGoodsAttr($fieldValue, $data)
    {
        if (! in_array($this->use_goods_type, [ CouponEnum::USE_GOODS_TYPE_ALLOW, CouponEnum::USE_GOODS_TYPE_BAN  ]) ) {
            return [];
        }
        return Goods::where('id', 'in', $this->use_goods_ids)
            ->field('id,code,name,image,min_price,max_price')
            ->select()->toArray();
    }
    
    function getGoodsCategoryAttr()
    {
        if ($this->use_goods_type != CouponEnum::USE_GOODS_TYPE_CATEGORY) {
            return [];
        }
        return GoodsCategory::where('id', 'in', $this->use_goods_category_ids)
            ->field('id,name,image')
            ->select()->toArray();
    }
    
    /**
     * @notes 商品使用类型
     * @param $fieldValue
     * @param $data
     * @return string
     * @author lbzy
     * @datetime 2023-11-06 10:16:02
     */
    function getUseTypeAttr($fieldValue, $data)
    {
        return CouponEnum::getUseGoodsTypeDesc($data['use_goods_type']);
    }
    
    /**
     * @notes tips
     * @param $fieldValue
     * @param $data
     * @return string
     * @author lbzy
     * @datetime 2023-11-07 18:23:44
     */
    function getTipsAttr($fieldValue, $data)
    {
        switch ($this->use_goods_type) {
            case CouponEnum::USE_GOODS_TYPE_NOT:
                $tips = '';
                break;
            case CouponEnum::USE_GOODS_TYPE_ALLOW:
                $goodsName = (new Goods())->whereIn('id', $this->use_goods_ids)->column('name');
                $tips = '仅限 ' . implode('、', $goodsName) . ' 商品可用';
                break;
            case CouponEnum::USE_GOODS_TYPE_BAN:
                $goodsName = (new Goods())->whereIn('id', $this->use_goods_ids)->column('name');
                $tips = '限制 ' . implode('、', $goodsName) . ' 商品不可用';
                break;
            case CouponEnum::USE_GOODS_TYPE_CATEGORY:
                $goodsName = (new GoodsCategory())->whereIn('id', $this->use_goods_category_ids)->column('name');
                $tips = '限制 ' . implode('、', $goodsName) . ' 分类可用';
                break;
        }
        
        return $tips ?? '';
    }
    
    /**
     * @notes status_desc 状态
     * @param $fieldValue
     * @param $data
     * @return string
     * @author lbzy
     * @datetime 2023-11-06 10:16:52
     */
    function getStatusDescAttr($fieldValue, $data)
    {
        return CouponEnum::getCouponStatusDesc($this->status);
    }
    
    /**
     * @notes status_text 状态
     * @param $fieldValue
     * @param $data
     * @return string
     * @author lbzy
     * @datetime 2023-11-06 10:16:52
     */
    function getStatusTextAttr($fieldValue, $data)
    {
        return CouponEnum::getCouponStatusDesc($this->status);
    }
    
    /**
     * @notes condition 使用条件
     * @param $fieldValue
     * @param $data
     * @return string
     * @author lbzy
     * @datetime 2023-11-06 10:23:58
     */
    function getconditionAttr($fieldValue, $data)
    {
        if (CouponEnum::CONDITION_TYPE_NOT == $this->condition_type) {
            return '无金额门槛';
        }
        
        return '满' . $this->condition_money . '使用';
    }
    
    /**
     * @notes discount_content 优惠信息
     * @param $fieldValue
     * @param $data
     * @return string
     * @author lbzy
     * @datetime 2023-11-06 10:28:01
     */
    function getDiscountContentAttr($fieldValue, $data)
    {
        switch ($this->condition_type) {
            case CouponEnum::CONDITION_TYPE_NOT:
                return '无金额门槛';
            case  CouponEnum::CONDITION_TYPE_FULL:
                return '满' . $this->condition_money . '使用';
            case CouponEnum::CONDITION_TYPE_DISCOUNT:
                return '满' . $this->condition_money . '打' . $this->discount_ratio . '折';
            default:
                return '';
        }
    }
    
    /**
     * @notes use_time_text
     * @return string|void
     * @author lbzy
     * @datetime 2023-11-06 10:39:41
     */
    function getUseTimeTextAttr($fieldValue, $data)
    {
        switch ($this->use_time_type) {
            case CouponEnum::USE_TIME_TYPE_FIXED:
                $start  = date('Y-m-d H:i:s', $this->use_time_start);
                $end    = date('Y-m-d H:i:s', $this->use_time_end);
                return $start . ' ~ ' . $end;
            case CouponEnum::USE_TIME_TYPE_TODAY:
                return '领取当日起' . $this->use_time . '天内可用';
            case CouponEnum::USE_TIME_TYPE_TOMORROW:
                return '领取次日起' . $this->use_time . '天内可用';
        }
    }
    
    /**
     * @notes use_time_text2 显示日期
     * @return string|void
     * @author lbzy
     * @datetime 2023-11-06 10:39:41
     */
    function getUseTimeText2Attr($fieldValue, $data)
    {
        switch ($this->use_time_type) {
            case CouponEnum::USE_TIME_TYPE_FIXED:
                $start  = date('Y-m-d', $this->use_time_start);
                $end    = date('Y-m-d', $this->use_time_end);
                return $start . ' ~ ' . $end;
            case CouponEnum::USE_TIME_TYPE_TODAY:
                return '领取当日起' . $this->use_time . '天内可用';
            case CouponEnum::USE_TIME_TYPE_TOMORROW:
                return '领取次日起' . $this->use_time . '天内可用';
        }
    }
    
    /**
     * @notes receive_number 领取数量
     * @param $fieldValue
     * @param $data
     * @return int
     * @author lbzy
     * @datetime 2023-11-06 10:49:57
     */
    function getReceiveNumberAttr($fieldValue, $data)
    {
        return CouponList::where('coupon_id', $data['id'])->count();
    }
    
    /**
     * @notes use_number 使用数量
     * @param $fieldValue
     * @param $data
     * @return int
     * @author lbzy
     * @datetime 2023-11-06 10:49:57
     */
    function getUseNumberAttr($fieldValue, $data)
    {
        return CouponList::where('coupon_id', $data['id'])->where('status', 1)->count();
    }
    
    /**
     * @notes surplus_number 限制
     * @param $fieldValue
     * @param $data
     * @return mixed|string
     * @author lbzy
     * @datetime 2023-11-06 10:50:30
     */
    function getSurplusNumberAttr($fieldValue, $data)
    {
        return $this->send_total_type == CouponEnum::SEND_TOTAL_TYPE_NOT
            ? '不限制'
            : $this->send_total - $this->getAttr('receive_number');
    }
    
    /**
     * @notes send_total_text
     * @param $fieldValue
     * @param $data
     * @return mixed|string
     * @author lbzy
     * @datetime 2023-11-06 10:54:59
     */
    function getSendTotalTextAttr($fieldValue,$data)
    {
        return $this->send_total_type == CouponEnum::SEND_TOTAL_TYPE_NOT ? '不限量' : $this->send_total;
    }
    
    /**
     * @notes is_available api模块用户是否可用
     * @return int
     * @author lbzy
     * @datetime 2023-11-06 14:16:56
     */
    function getIsAvailableAttr($fieldValue,$data)
    {
        switch ($data['get_num_type']) {
            case CouponEnum::GET_NUM_TYPE_NOT:
                return 0;
            case CouponEnum::GET_NUM_TYPE_LIMIT:
                $total = (new CouponList())
                    ->where(['coupon_id' => $data['id']])
                    ->where(['user_id' => request()->userId ])
                    ->count();
                return $total >= $data['get_num'] ? 1 : 0;
            case CouponEnum::GET_NUM_TYPE_DAY:
                $total = (new CouponList())
                    ->where(['coupon_id' => $data['id']])
                    ->where(['user_id' => request()->userId ])
                    ->where('create_time', '>=', TimeService::today()[0])
                    ->where('create_time', '<=', TimeService::today()[1])
                    ->count();
                return $total >= $data['get_num'] ? 1 : 0;
        }
        
        return 0;
    }
}