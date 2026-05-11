<?php
// +----------------------------------------------------------------------
// | LikeShop有特色的全开源社交分销电商系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 商业用途务必购买系统授权，以免引起不必要的法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | 微信公众号：好象科技
// | 访问官网：http://www.likemarket.net
// | 访问社区：http://bbs.likemarket.net
// | 访问手册：http://doc.likemarket.net
// | 好象科技开发团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | Author: LikeShopTeam-段誉
// +----------------------------------------------------------------------


namespace app\shopapi\controller;


use app\adminapi\logic\withdraw\WechatMerchantTransferLogic;
use app\common\model\UserAuth;
use app\common\model\WithdrawApply;
use app\common\service\WeChatConfigService;

/**
 * 测试控制器
 * Class TestController
 * @package app\shopapi\controller
 */
class TestController extends BaseShopController
{
    public array $notNeedLogin = ['test'];

    public function test()
    {
        $withdrawApply = WithdrawApply::findOrEmpty(32)->toArray();
        $userAuth = UserAuth::where('user_id', $withdrawApply['user_id'])->order('terminal', 'asc')->findOrEmpty();
        $config = WeChatConfigService::getWechatConfigByTerminal(1);
//        WechatMerchantTransferLogic::transfer($withdrawApply,$userAuth,$config);
        WechatMerchantTransferLogic::details($withdrawApply,$config);
        halt(111);
    }
}