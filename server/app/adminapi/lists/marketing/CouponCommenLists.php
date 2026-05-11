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
namespace app\adminapi\lists\marketing;
use app\adminapi\lists\BaseAdminDataLists;
use app\common\{enum\CouponEnum, model\Coupon, lists\ListsSearchInterface, model\CouponList};

/**
 * 优惠券公共列表
 * Class CouponCommenLists
 * @package app\adminapi\lists\marketing
 */
class CouponCommenLists extends BaseAdminDataLists
{
    function setSearch(): array
    {
        $where = [];
    
        $where[] = ['get_type','=', $this->params['get_type'] ?? CouponEnum::GET_TYPE_USER ];
        
        if(isset($this->params['name'])){
            $where[] = ['name','like','%'.$this->params['name'].'%'];
        }
        
        $status = $this->params['status'] ?? '';
        
        //默认获取进行中的状态
        if(empty($status)){
            $status = [CouponEnum::COUPON_STATUS_NOT,CouponEnum::COUPON_STATUS_CONDUCT];
        }
        $where[] = ['status','in',$status];
        
        return $where;
    }


    public function lists(): array
    {
        $lists = Coupon::where($this->setSearch())
            ->limit($this->limitOffset, $this->limitLength)
            ->append([ 'use_type', 'discount_content', 'status_desc', 'condition', 'surplus_number' ])
            ->order('id', 'desc')
            ->field('id,name,money,discount_ratio,condition_type,condition_money,use_goods_type,status')
            ->select();

        return $lists->toArray();
    }

    public function count(): int
    {
        $count = Coupon::where($this->setSearch())
                ->count();
        return $count;
    }

}