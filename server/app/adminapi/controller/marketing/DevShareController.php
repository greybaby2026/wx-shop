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

namespace app\adminapi\controller\marketing;


use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\marketing\DevShareLogic;
use app\adminapi\validate\marketing\DevShareValidate;

class DevShareController extends BaseAdminController
{
    /**
     * @notes 列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2024/9/9 下午5:27
     */
    public function lists()
    {
        $params = $this->request->get();
        $result = (new DevShareLogic())->lists($params);
        return $this->data($result);
    }

    /**
     * @notes 编辑
     * @return \think\response\Json
     * @author ljj
     * @date 2024/9/9 下午5:35
     */
    public function edit()
    {
        $params = (new DevShareValidate())->post()->goCheck('edit');
        (new DevShareLogic())->edit($params);
        return $this->success('修改成功',[],1,1);
    }

    /**
     * @notes 详情
     * @return \think\response\Json
     * @author ljj
     * @date 2024/9/9 下午5:35
     */
    public function detail()
    {
        $params = (new DevShareValidate())->goCheck('detail');
        $result = (new DevShareLogic())->detail($params);
        return $this->success('',$result);
    }
}