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

namespace app\index\controller;

use app\common\service\ConfigService;

class IndexController
{
    /**
     * @notes 域名访问默认控制器
     * @return string|\think\response\View
     * @author 令狐冲
     * @date 2021/7/23 11:01
     */
    public function index()
    {
        $template = app()->getRootPath() . 'public/pc/index.html';
        if (is_mobile()) {
            $template = app()->getRootPath() . 'public/mobile/index.html';
        }
        if (file_exists($template)) {
            return view($template);
        }
        return '敬请期待';
    }

    /**
     * @notes APP下载链接跳转
     * @author Tab
     * @date 2021/12/30 14:44
     */
    public function app()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
            $url = ConfigService::get('app', 'ios_download_url', '');
        } else {
            $url = ConfigService::get('app', 'android_download_url', '');
        }

        if (empty($url)) {
            echo '未设置app下载链接';
            exit;
        }
        if (!preg_match("/^http(s)?:\\/\\/.+/", $url)) {
            $url = "http://".$url;
        }

        return redirect($url);
    }
}