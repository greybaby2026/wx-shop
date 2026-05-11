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
use app\adminapi\logic\marketing\PresellLogic;
use app\adminapi\validate\marketing\PresellValidate;

class PresellController extends BaseAdminController
{
    /**
     * @notes 预售列表
     * @return \think\response\Json
     * @author lbzy
     * @datetime 2024-04-24 09:10:01
     */
    function lists()
    {
        return $this->dataLists();
    }
    
    /**
     * @notes 预售添加
     * @return \think\response\Json
     * @author lbzy
     * @datetime 2024-04-24 09:15:28
     */
    function add()
    {
        $params = (new PresellValidate())->post()->goCheck('add');
    
        $result = PresellLogic::add($params);
        if ($result === true) {
            return $this->success('添加成功', [],1, 1);
        }
        
        return $this->fail(PresellLogic::getError());
    }
    
    /**
     * @notes 预售编辑
     * @return \think\response\Json
     * @author lbzy
     * @datetime 2024-04-24 14:23:42
     */
    function edit()
    {
        $params = (new PresellValidate())->post()->goCheck('edit');
    
        $result = PresellLogic::edit($params);
        if ($result === true) {
            return $this->success('编辑成功', [],1, 1);
        }
    
        return $this->fail(PresellLogic::getError());
    }
    
    /**
     * @notes 预售详情
     * @return \think\response\Json
     * @author lbzy
     * @datetime 2024-04-24 17:34:22
     */
    function detail()
    {
        (new PresellValidate())->goCheck('detail');
        return $this->success('成功', PresellLogic::detail(input()));
    }
    
    /**
     * @notes 开始活动
     * @return \think\response\Json
     * @author lbzy
     * @datetime 2024-04-24 17:38:19
     */
    function start()
    {
        $params = (new PresellValidate())->post()->goCheck('detail');
    
        $result = PresellLogic::start($params);
        if ($result === true) {
            return $this->success('开始成功', [], 1, 1);
        }
    
        return $this->fail(PresellLogic::getError());
    }
    
    /**
     * @notes 结束活动
     * @return \think\response\Json
     * @author lbzy
     * @datetime 2024-04-24 17:44:56
     */
    function end()
    {
        $params = (new PresellValidate())->post()->goCheck('detail');
    
        $result = PresellLogic::end($params);
        if ($result === true) {
            return $this->success('结束成功', [], 1, 1);
        }
    
        return $this->fail(PresellLogic::getError());
    }
    
    /**
     * @notes 删除活动
     * @return \think\response\Json
     * @author lbzy
     * @datetime 2024-04-24 17:47:02
     */
    function delete()
    {
        $params = (new PresellValidate())->post()->goCheck('detail');
    
        $result = PresellLogic::delete($params);
        if ($result === true) {
            return $this->success('删除成功', [], 1, 1);
        }
    
        return $this->fail(PresellLogic::getError());
    }
}