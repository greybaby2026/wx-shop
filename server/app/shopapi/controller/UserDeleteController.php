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
namespace app\shopapi\controller;

use app\shopapi\logic\UserDeleteLogic;

/**
 * @notes 用户注销
 * author lbzy
 * @datetime 2023-08-21 13:23:10
 * @class UserDeleteController
 * @package app\shopapi\controller
 */
class UserDeleteController extends BaseShopController
{
    /**
     * @notes 检测是否可注销
     * @return \think\response\Json
     * @author lbzy
     * @datetime 2023-08-21 16:23:31
     */
    function check(): \think\response\Json
    {
        return $this->success('成功', UserDeleteLogic::checkCanDelete($this->userId));
    }
    
    /**
     * @notes 确定注销
     * @return \think\response\Json
     * @author lbzy
     * @datetime 2023-08-21 16:46:32
     */
    function delete()
    {
        $result = UserDeleteLogic::sureDelete($this->userId);
        
        if ($result !== true) {
            return $this->fail((string) $result);
        }
    
        return $this->success('成功');
    }
}