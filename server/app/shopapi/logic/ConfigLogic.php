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
namespace app\shopapi\logic;
use app\common\cache\HandleConcurrencyCache;
use app\common\enum\DistributionConfigEnum;
use app\common\model\DevShare;
use app\common\model\DistributionConfig;
use app\common\service\{
    FileService,
    ConfigService,

};

/**
 * 配置逻辑层
 * Class CollectLogic
 * @package app\common\logic
 */
class ConfigLogic
{
    /**
     * @notes 获取商城配置
     * @return array
     * @author cjhao
     * @date 2021/8/28 17:23
     */
    public function getConfig():array
    {
        $share_image    = ConfigService::get('shop', 'share_image');
        $favicon        = ConfigService::get('shop', 'favicon');
        
        $config = [
            //注册方式
            'register_way'              => ConfigService::get('config', 'register_way',  config('project.login.register_way')),
            //登录方式
            'login_way'                 => ConfigService::get('config', 'login_way',config('project.login.login_way')),
            //手机号码注册需验证码
            'is_mobile_register_code'   => ConfigService::get('config', 'is_mobile_register_code',  config('project.login.is_mobile_register_code')),
            //注册强制绑定手机
            'coerce_mobile'             => ConfigService::get('config', 'coerce_mobile', config('project.login.coerce_mobile')),
            //公众号微信授权登录
            'h5_wechat_auth'            => ConfigService::get('config', 'h5_wechat_auth', config('project.login.h5_wechat_auth')),
            //公众号自动微信授权登录
            'h5_auto_wechat_auth'       => ConfigService::get('config', 'h5_auto_wechat_auth', config('project.login.h5_auto_wechat_auth')),
            //小程序微信授权登录
            'mnp_wechat_auth'           => ConfigService::get('config', 'mnp_wechat_auth', config('project.login.mnp_wechat_auth')),
            //小程序自动微信授权登录
            'mnp_auto_wechat_auth'      => ConfigService::get('config', 'mnp_auto_wechat_auth',  config('project.login.mnp_auto_wechat_auth')),
            //字节小程序授权登录
            'toutiao_auth'              => ConfigService::get('config', 'toutiao_auth', config('project.login.toutiao_auth')),
            //字节小程序自动授权登录
            'toutiao_auto_auth'         => ConfigService::get('config', 'toutiao_auto_auth',  config('project.login.toutiao_auto_auth')),
            //APP微信授权登录
            'app_wechat_auth'           => ConfigService::get('config', 'app_wechat_auth',  config('project.login.app_wechat_auth')),
            //oss域名
            'oss_domain'                => FileService::getFileUrl(),
            //店铺logo
            'logo'                      => FileService::getFileUrl(ConfigService::get('shop', 'mobile_logo')),
            //分享页面
            'share_page'                => ConfigService::get('shop', 'share_page'),
            //分享标题
            'share_title'               => ConfigService::get('shop', 'share_title', ''),
            //分享简介
            'share_intro'               => ConfigService::get('shop', 'share_intro', ''),
            //分享图片
            'share_image'               => $share_image ? FileService::getFileUrl($share_image) : '',
            // 小程序商城关闭状态
            'mnp_status'                => ConfigService::get('shop', 'status', 1),
            // H5商城关闭状态
            'h5_status'                 => ConfigService::get('h5', 'status', 1),
            // PC商城关闭状态
            'pc_status'                 => ConfigService::get('pc', 'status', 1),
            // 版权信息
            'copyright'                 => ConfigService::get('shop', 'copyright', ''),
            // 备案号
            'record_number'             => ConfigService::get('shop', 'record_number', ''),
            // 备案链接
            'record_system_link'             => ConfigService::get('shop', 'record_system_link', ''),
            // 客服请求域名
            'ws_domain'                 => env('project.ws_domain', 'ws:127.0.0.1'),
            //app协议弹出
            'app_pop_agreement'         => ConfigService::get('app','pop_agreement',1),
//            app微信登录
//            'wechat_login'          => ConfigService::get('app','wechat_login',1),
            'area_js'                   => '/resource/js/area.js',
            'shop_name'                 => ConfigService::get('shop', 'name'),
            
            'domain'                    => request()->domain(true),
            
            'favicon'                   => $favicon ? FileService::getFileUrl($favicon) : '',
    
            // 发货配置
            'mini_express_send_sync'    => ConfigService::get('mini_program', 'express_send_sync', 1),
            // 站点统计
            'site_statistic'    => [
                'clarity_app_id'  => ConfigService::get('site_statistic', 'clarity_app_id', '')
            ],
        ];
        return $config;
    }

    /**
     * @notes 获取政策协议
     * @return array|string
     * @author Tab
     * @date 2021/7/28 16:08
     */
    public function getPolicyAgreement()
    {
        $params = request()->get();
        // 服务协议
        if (isset($params['type']) && $params['type'] == 1) {
            return [
                'content'   => ConfigService::get('shop', 'service_agreement_content', ''),
                'name'      => ConfigService::get('shop', 'service_agreement_name', ''),
            ];
        }
        // 隐私政策
        if (isset($params['type']) && $params['type'] == 2) {
            return [
                'content'   => ConfigService::get('shop', 'privacy_policy_content', ''),
                'name'      => ConfigService::get('shop', 'privacy_policy_name', ''),
            ];
        }
        // 分销协议
        if (isset($params['type']) && $params['type'] == 3) {
            $dbConfig = DistributionConfig::column('value', 'key');
            
            return [
                'content'   => $dbConfig['protocol_content'] ?? DistributionConfigEnum::DEFAULT_PROTOCOL_CONTENT,
                'name'      => $dbConfig['protocol_name'] ?? '分销协议',
            ];
        }
        
        //注销协议
        if (isset($params['type']) && $params['type'] == 4) {
            return [
                'content'   => ConfigService::get('shop', 'user_delete_content', ''),
                'name'      => ConfigService::get('shop', 'user_delete_name', ''),
            ];
        }

        return [ 'content' => '', 'name' => '' ];
    }


    /**
     * @notes 版权信息
     * @return array|int|mixed|string|null
     * @author ljj
     * @date 2022/2/22 11:49 上午
     */
    public function copyright()
    {
        return [
            'copyright'             => ConfigService::get('shop', 'copyright', ''),
            'copyright2'            => ConfigService::get('shop', 'copyright2', ''),
            'copyright_auth'        => ConfigService::get('shop', 'copyright_auth', []) ? : [
                'type'  => "1",
                'list' => [
                    [ 'image' => '', 'name' =>'', 'link' => '' ],
                ]
            ],
        ];
    }


    /**
     * @notes 获取客服配置
     * @return array
     * @author ljj
     * @date 2024/8/28 下午4:06
     */
    public function getKefuConfig()
    {
        $defaultData = [
            'way' => 1,
            'name' => '',
            'remarks' => '',
            'phone' => '',
            'business_time' => '',
            'qr_code' => '',
            'enterprise_id' => '',
            'kefu_link' => ''
        ];
        $config = [
            'mnp' => ConfigService::get('kefu_config', 'mnp', $defaultData),
            'oa' => ConfigService::get('kefu_config', 'oa', $defaultData),
            'h5' => ConfigService::get('kefu_config', 'h5', $defaultData),
            'pc' => ConfigService::get('kefu_config', 'pc', $defaultData),
            'app' => ConfigService::get('kefu_config', 'app', $defaultData),
        ];
        if (!empty($config['mnp']['qr_code'])) $config['mnp']['qr_code'] = FileService::getFileUrl($config['mnp']['qr_code']);
        if (!empty($config['oa']['qr_code'])) $config['oa']['qr_code'] = FileService::getFileUrl($config['oa']['qr_code']);
        if (!empty($config['h5']['qr_code'])) $config['h5']['qr_code'] = FileService::getFileUrl($config['h5']['qr_code']);
        if (!empty($config['pc']['qr_code'])) $config['pc']['qr_code'] = FileService::getFileUrl($config['pc']['qr_code']);
        if (!empty($config['app']['qr_code'])) $config['app']['qr_code'] = FileService::getFileUrl($config['app']['qr_code']);

        return $config;
    }

    /**
     * @notes 获取分享配置
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2024/9/11 下午3:20
     */
    public function getShareConfig()
    {
        //读取缓存
        $HandleConcurrencyCache = new HandleConcurrencyCache();
        $lists = $HandleConcurrencyCache->getCache($HandleConcurrencyCache->getShareConfigKey());
        if ($lists !== false) {
            return $lists;
        }

        $lists = DevShare::field('*')
            ->append(['type_desc','page_desc'])
            ->select()
            ->toArray();

        //设置缓存
        $HandleConcurrencyCache->setCache($HandleConcurrencyCache->getShareConfigKey(),$lists);

        return $lists;
    }
}