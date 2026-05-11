<?php
// +----------------------------------------------------------------------
// | LikeShop100%开源免费商用电商系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | Gitee下载：https://gitee.com/likeshop_gitee/likeshop
// | 访问官网：https://www.likemarket.net
// | 访问社区：https://home.likemarket.net
// | 访问手册：http://doc.likemarket.net
// | 微信公众号：好象科技
// | 好象科技开发团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------

// | Author: LikeShopTeam
// +----------------------------------------------------------------------
namespace app\common\service;

use app\common\enum\PayEnum;
use app\common\enum\UserTerminalEnum;
use app\common\model\PayConfig;

/**
 * 微信配置类
 * Class WeChatConfigService
 * @package app\common\service
 */
class WeChatConfigService
{
    /**
     * 获取小程序配置
     * @return array
     */
    public static function getMnpConfig()
    {
        $config = [
            'app_id' => ConfigService::get('mini_program', 'app_id',''),
            'secret' => ConfigService::get('mini_program', 'app_secret','' ),
            'mch_id' => ConfigService::get('mini_program', 'mch_id'),
            'key' => ConfigService::get('mini_program', 'key'),
            'response_type' => 'array',
            'log' => [
                'default' => 'prod', // 默认使用的 channel，生产环境可以改为下面的 prod
                'channels' => [
                    // 测试环境
                    'dev' => [
                        'driver'    => 'daily',
                        'path'      => app()->getRuntimePath() . 'easywechat.log',
                        'level'     => 'debug',
                    ],
                    // 生产环境
                    'prod' => [
                        'driver'    => 'daily',
                        'path'      => app()->getRuntimePath() . 'easywechat.log',
                        'level'     => 'info',
                    ],
                ],
            ],
        ];
        return $config;
    }

    /**
     * 获取微信公众号配置
     * @return array
     */
    public static function getOaConfig()
    {
        $config = [
            'app_id' => ConfigService::get('official_account', 'app_id',''),
            'secret' => ConfigService::get('official_account', 'app_secret',''),
            'mch_id' => ConfigService::get('official_account', 'mch_id'),
            'key' => ConfigService::get('official_account', 'key'),
            'token' => ConfigService::get('official_account', 'token',''),
            'response_type' => 'array',
            'log' => [
                'default' => 'prod', // 默认使用的 channel，生产环境可以改为下面的 prod
                'channels' => [
                    // 测试环境
                    'dev' => [
                        'driver'    => 'daily',
                        'path'      => app()->getRuntimePath() . 'easywechat.log',
                        'level'     => 'debug',
                    ],
                    // 生产环境
                    'prod' => [
                        'driver'    => 'daily',
                        'path'      => app()->getRuntimePath() . 'easywechat.log',
                        'level'     => 'info',
                    ],
                ],
            ],
        ];
        return $config;
    }

    /**
     * 获取微信开放平台应用配置
     * @return array
     */
    public static function getOpConfig()
    {
        $config = [
            'app_id' => ConfigService::get('op', 'app_id'),
            'secret' => ConfigService::get('op', 'secret'),
            'response_type' => 'array',
            'log' => [
                'default' => 'prod', // 默认使用的 channel，生产环境可以改为下面的 prod
                'channels' => [
                    // 测试环境
                    'dev' => [
                        'driver'    => 'daily',
                        'path'      => app()->getRuntimePath() . 'easywechat.log',
                        'level'     => 'debug',
                    ],
                    // 生产环境
                    'prod' => [
                        'driver'    => 'daily',
                        'path'      => app()->getRuntimePath() . 'easywechat.log',
                        'level'     => 'info',
                    ],
                ],
            ],
        ];
        return $config;
    }





    //根据用户客户端不同获取不同的微信配置
    public static function getWechatConfigByTerminal($terminal)
    {
        switch ($terminal) {
            case UserTerminalEnum::WECHAT_MMP:
                $appid      = ConfigService::get('mini_program', 'app_id');
                $secret     = ConfigService::get('mini_program', 'app_secret');
                $notify_url = (string) url('pay/notifyMnp', [], false, true);
                break;
            case UserTerminalEnum::WECHAT_OA:
            case UserTerminalEnum::PC:
            case UserTerminalEnum::H5:
                $appid      = ConfigService::get('official_account', 'app_id');
                $secret     = ConfigService::get('official_account', 'app_secret');
                $notify_url = (string) url('pay/notifyOa', [], false, true);
                break;
            case UserTerminalEnum::ANDROID:
            case UserTerminalEnum::IOS:
                $appid      = ConfigService::get('open_platform', 'app_id');
                $secret     = ConfigService::get('open_platform', 'app_secret');
                $notify_url = (string) url('pay/notifyApp', [], false, true);
                break;
            default:
                $appid = '';
                $secret = '';
        }

        $pay = PayConfig::where(['pay_way' => PayEnum::WECHAT_PAY])->findOrEmpty()->toArray();
        
        //写入文件
        $save_path = app()->getRuntimePath() . 'certificates';
        file_exists($save_path) || mkdir($save_path, 0775, true);
        
        $apiclient_cert         = $pay['config']['apiclient_cert'] ?? '';
        $apiclient_key          = $pay['config']['apiclient_key'] ?? '';
        $wechat_public_cert     = $pay['config']['wechat_public_cert'] ?? '';
        
        $prefix = implode('-', [
            "{$save_path}/sid",
            $pay['config']['interface_version'] ?? '',
            $pay['config']['merchant_type'] ?? '',
        ]);
        
        $cert_path              = $prefix . '-' . md5($apiclient_cert) . '.pem';
        $key_path               = $prefix . '-' . md5($apiclient_key) . '.pem';
        $wechat_public_path     = $prefix . '-' . md5($wechat_public_cert) . '.pem';
        
        file_exists($cert_path) || file_put_contents($cert_path, $apiclient_cert);
        file_exists($key_path) || file_put_contents($key_path, $apiclient_key);
        file_exists($wechat_public_path) || file_put_contents($wechat_public_path, $wechat_public_cert);
        
        $log = request()->isCli() ? '-command' : '';
        
        $config = [
            'app_id'                => $appid,
            'secret'                => $secret,
            'mch_id'                => $pay['config']['mch_id'] ?? '',
            'key'                   => $pay['config']['pay_sign_key'] ?? '',
            'cert_path'             => $cert_path,
            'key_path'              => $key_path,
            'interface_version'     => $pay['config']['interface_version'] ?? '',
            'merchant_type'         => $pay['config']['merchant_type'] ?? '',
            'response_type'         => 'array',
            'wechat_public_serial'  => $pay['config']['wechat_public_serial'] ?? '',
            'wechat_public_cert'    => $wechat_public_cert,
            'log' => [
                'default' => 'prod', // 默认使用的 channel，生产环境可以改为下面的 prod
                'channels' => [
                    // 测试环境
                    'dev' => [
                        'driver'    => 'daily',
                        'path'      => app()->getRuntimePath() . 'easywechat' . $log . '.log',
                        'level'     => 'debug',
                    ],
                    // 生产环境
                    'prod' => [
                        'driver'    => 'daily',
                        'path'      => app()->getRuntimePath() . 'easywechat' . $log . '.log',
                        'level'     => 'info',
                    ],
                ],
            ],
            'notify_url' => $notify_url
        ];
        
        return $config;
    }

}