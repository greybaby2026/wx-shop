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
namespace app\shopapi\lists;

use app\common\{cache\HandleConcurrencyCache,
    enum\UserTerminalEnum,
    model\Goods,
    enum\GoodsEnum,
    model\SearchRecord,
    service\ConfigService,
    service\JiebaService};
use think\facade\Db;

/**
 * 商品列表接口
 * Class GoodsLists
 * @package app\shopapi\lists
 */
class GoodsLists extends BaseShopDataLists
{
    protected $whereRaw = [];

    public function setSearch(): array
    {
        return array_diff(array_keys($this->params), ['page_no', 'page_size','name']);
    }


    /**
     * @notes 名称搜索优化
     */
    public function whereRaw()
    {
        if (!empty($this->params['name']) && empty($this->whereRaw)) {
            $cutResult = (new JiebaService())->cut($this->params['name'], false, 1);

            // 使用参数绑定来防止SQL注入
            $condition[] = "name LIKE ?";
            $bind[] = "%{$this->params['name']}%";
            foreach ($cutResult as $t) {
                $condition[] = "name LIKE ?";
                $bind[] = "%{$t}%";
            }
            $condition = implode(' OR ', $condition);

            $this->whereRaw = ['condition'=>$condition, 'bind'=>$bind];
        }
        return $this->whereRaw;
    }


    /**
     * @note 首页商品列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2021/7/26 16:47
     */
    public function lists(): array
    {
        //读取缓存
        $HandleConcurrencyCache = new HandleConcurrencyCache();
        $list = $HandleConcurrencyCache->getCache($HandleConcurrencyCache->getGoodsListsKey(md5(serialize($this->params))));
        if ($list !== false) {
            return $list;
        }

        $this->whereRaw();
        $whereRaw = $this->whereRaw;
        $list = Goods::withSearch($this->setSearch(), $this->params)
            ->field('id,name,image,virtual_sales_num+sales_num as sales_num,min_price as sell_price,min_lineation_price as lineation_price')
            ->where(['status'=>GoodsEnum::STATUS_SELL])
            ->when(!empty($whereRaw['condition']), function ($query) use ($whereRaw) {
                $query->whereRaw($whereRaw['condition'], $whereRaw['bind']);
            })
            ->limit($this->limitOffset, $this->limitLength)
            ->order('sort desc,id desc')
            ->select();

        $showPrice = ConfigService::get('goods_set', 'show_price', 1);
        $is_sales_num = ConfigService::get('goods_set', 'sales_num', 1);

        foreach ($list as &$goods){
            if(0 == $showPrice){
                $goods['lineation_price'] = 0;
            }
            $goods['is_sales_num'] = $is_sales_num;
        }

        $name = $this->params['name'] ?? '';
        if ($name && $this->userId && $this->userInfo['terminal'] != UserTerminalEnum::PC) {//记录关键词
            $this->recordKeyWord(trim($name), $this->userId);
        }

        //设置缓存
        $HandleConcurrencyCache->setCache($HandleConcurrencyCache->getGoodsListsKey(md5(serialize($this->params))),$list->toArray(),600);

        return $list->toArray();

    }

    /**
     * @notes 商品统计
     * @return int
     * @author cjhao
     * @date 2021/7/26 16:48
     */
    public function count(): int
    {
        $whereRaw = $this->whereRaw;
        return Goods::withSearch($this->setSearch(), $this->params)
            ->where(['status'=>GoodsEnum::STATUS_SELL])
            ->when(!empty($whereRaw['condition']), function ($query) use ($whereRaw) {
                $query->whereRaw($whereRaw['condition'], $whereRaw['bind']);
            })
            ->count();

    }

    /**
     * @notes 记录商品搜索
     * @param $name
     * @param $userId
     * @return bool
     * @author cjhao
     * @date 2021/8/11 17:43
     */
    public function recordKeyWord($name,$userId):bool
    {
        //删除缓存
        $HandleConcurrencyCache = new HandleConcurrencyCache();
        $HandleConcurrencyCache->deleteCache($HandleConcurrencyCache->getGoodsSearchRecordKey($userId));

        $searchRecord = SearchRecord::where(['user_id'=>$userId,'keyword'=>$name])->findOrEmpty();

        if($searchRecord->isEmpty()){
            SearchRecord::create([
                'user_id' => $userId,
                'keyword' => $name,
            ]);
            return true;
        }
        $searchRecord->count = Db::raw('count+1');
        $searchRecord->save();

        return true;
    }
}