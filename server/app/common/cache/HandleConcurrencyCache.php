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

namespace app\common\cache;

use think\facade\Cache;

/**
 * 处理高并发缓存
 */
class HandleConcurrencyCache extends BaseCache
{
    protected $tagName = 'concurrency';

    /**
     * @notes 获取缓存
     * @param $cacheName //缓存名称
     * @return false|mixed
     * @author ljj
     * @date 2025/2/20 下午2:50
     */
    public function getCache($cacheName)
    {
        $result = $this->get($this->tagName.'-'.$cacheName);
        if($result) {
            return $result;
        }
        return false;
    }

    /**
     * @notes 设置缓存
     * @param $cacheName //缓存名称
     * @param $data //缓存数据
     * @param $expire //缓存过期时间
     * @author ljj
     * @date 2025/2/20 下午2:50
     */
    public function setCache($cacheName,$data, $expire = null)
    {
        //设置新缓存
        $this->set($this->tagName.'-'.$cacheName, $data, $expire);
    }


    /**
     * @notes 删除缓存
     * @param $cacheName
     * @author ljj
     * @date 2025/2/20 下午8:09
     */
    public function deleteCache($cacheName)
    {
        //删除旧缓存
        $this->delete($this->tagName.'-'.$cacheName);
    }


    /**
     * @notes 清除指定前缀的缓存
     * @param $cacheName
     * @return true
     * @author ljj
     * @date 2025/2/20 下午9:03
     */
    public function clearPrefix($cacheName)
    {
        $keys = $this->get($this->tagName.'-'.$cacheName.'*');
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    /**
     * @notes 校验并获取redis缓存对象
     * @return false|\think\cache\Driver
     * @author ljj
     * @date 2025/3/24 下午5:50
     */
    public function getRedis()
    {
        try {
            // 尝试连接 Redis
            $redis = Cache::store('redis')->handler();
            $redis->ping(); // 测试连接，成功返回 +PONG

            return Cache::store('redis');
        } catch (\Exception $e) {
            return false;
        }
    }


    //装修主题配置缓存键
    public function getDecorateThemeConfigKey()
    {
        return 'decorate_theme_config';
    }

    //分享配置缓存键
    public function getShareConfigKey()
    {
        return 'share_config';
    }

    //首页装修缓存键
    public function getDecorateThemeHomeKey()
    {
        return 'decorate_theme_home';
    }

    //装修页面缓存键
    public function getDecorateThemePageKey($type,$userId,$goodsId)
    {
        return 'decorate_theme_page_'.$type.$userId.$goodsId;
    }

    //商品列表缓存键
    public function getGoodsListsKey($suffix)
    {
        return 'goods_lists_'.$suffix;
    }

    //商品搜索记录缓存键
    public function getGoodsSearchRecordKey($user_id)
    {
        return 'goods_search_record_'.$user_id;
    }

    //下单限流缓存键
    public function getSubmitOrderLimitKey($user_id)
    {
        return 'submit_order_limit_'.$user_id;
    }
}