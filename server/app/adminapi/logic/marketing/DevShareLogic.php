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

use app\common\cache\HandleConcurrencyCache;
use app\common\logic\BaseLogic;
use app\common\model\DevShare;

class DevShareLogic extends BaseLogic
{
    /**
     * @notes 列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2024/9/9 下午5:27
     */
    public function lists($params)
    {
        $where = [];
        if (isset($params['type']) && $params['type'] != '') {
            $where[] = ['type','=',$params['type']];
        }

        $lists = DevShare::field('*')
            ->where($where)
            ->append(['type_desc','page_desc'])
            ->select()
            ->toArray();

        return $lists;
    }

    /**
     * @notes 编辑
     * @param $params
     * @return true
     * @author ljj
     * @date 2024/9/9 下午5:35
     */
    public function edit($params)
    {
        DevShare::update([
            'id' => $params['id'],
            'title' => $params['title'] ?? '',
            'synopsis' => $params['synopsis'] ?? '',
            'image' => $params['image'] ?? '',
        ]);

        //删除缓存
        $HandleConcurrencyCache = new HandleConcurrencyCache();
        $HandleConcurrencyCache->deleteCache($HandleConcurrencyCache->getShareConfigKey());

        return true;
    }

    /**
     * @notes 详情
     * @param $params
     * @return array
     * @author ljj
     * @date 2024/9/9 下午5:35
     */
    public function detail($params)
    {
        $result = DevShare::findOrEmpty($params['id'])->toArray();
        return $result;
    }
}