<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\common\service\pay;

use Yansongda\Pay\Pay;

class YansongdaPay
{

    /**
     * 文档 https://pay.yansongda.cn
     * @notes
     * @author lbzy
     * @DateTime 2022-11-08T18:03:00+0800
     */
    static function getWechatConfig(array $wechatTerminalConfig) : array
    {
        $time = time();
        
        return  [
            'alipay' => [
                'default' => [
                    // 「必填」支付宝分配的 app_id
                    'app_id' => '',
                    // 「必填」应用私钥 字符串或路径
                    // 在 https://open.alipay.com/develop/manage 《应用详情->开发设置->接口加签方式》中设置
                    'app_secret_cert' => '',
                    // 「必填」应用公钥证书 路径
                    // 设置应用私钥后，即可下载得到以下3个证书
                    'app_public_cert_path' => '',
                    // 「必填」支付宝公钥证书 路径
                    'alipay_public_cert_path' => '',
                    // 「必填」支付宝根证书 路径
                    'alipay_root_cert_path' => '',
                    'return_url' => '',
                    'notify_url' => '',
                    // 「选填」第三方应用授权token
                    'app_auth_token' => '',
                    // 「选填」服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数
                    'service_provider_id' => '',
                    // 「选填」默认为正常模式。可选为： MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
                    'mode' => Pay::MODE_NORMAL,
                ]
            ],
            'wechat' => [
                'default' => [
                    // 「必填」商户号，服务商模式下为服务商商户号
                    // 可在 https://pay.weixin.qq.com/ 账户中心->商户信息 查看
                    'mch_id' => $wechatTerminalConfig['mch_id'],
                    // 「选填」v2商户私钥
                    'mch_secret_key_v2' => '',
                    // 「必填」v3 商户秘钥
                    // 即 API v3 密钥(32字节，形如md5值)，可在 账户中心->API安全 中设置
                    'mch_secret_key' => $wechatTerminalConfig['key'],
                    // 「必填」商户私钥 字符串或路径
                    // 即 API证书 PRIVATE KEY，可在 账户中心->API安全->申请API证书 里获得
                    // 文件名形如：apiclient_key.pem
                    'mch_secret_cert' => $wechatTerminalConfig['key_path'],
                    // 「必填」商户公钥证书路径
                    // 即 API证书 CERTIFICATE，可在 账户中心->API安全->申请API证书 里获得
                    // 文件名形如：apiclient_cert.pem
                    'mch_public_cert_path' => $wechatTerminalConfig['cert_path'],
                    // 「必填」微信回调url
                    // 不能有参数，如?号，空格等，否则会无法正确回调
                    'notify_url'    => $wechatTerminalConfig['notify_url'],
                    // 「选填」公众号 的 app_id
                    // 可在 mp.weixin.qq.com 设置与开发->基本配置->开发者ID(AppID) 查看
                    'mp_app_id'     => $wechatTerminalConfig['app_id'],
                    // 「选填」小程序 的 app_id
                    'mini_app_id'   => $wechatTerminalConfig['app_id'],
                    // 「选填」app 的 app_id
                    'app_id'        => $wechatTerminalConfig['app_id'],
                    // 「选填」服务商模式下，子公众号 的 app_id
                    'sub_mp_app_id'     => isset($wechatTerminalConfig['sub_config']) ? $wechatTerminalConfig['sub_config']['app_id']: '',
                    // 「选填」服务商模式下，子 app 的 app_id
                    'sub_app_id'        => isset($wechatTerminalConfig['sub_config']) ? $wechatTerminalConfig['sub_config']['app_id']: '',
                    // 「选填」服务商模式下，子小程序 的 app_id
                    'sub_mini_app_id'   => isset($wechatTerminalConfig['sub_config']) ? $wechatTerminalConfig['sub_config']['app_id']: '',
                    // 「选填」服务商模式下，子商户id
                    'sub_mch_id'        => $wechatTerminalConfig['sub_mch_id'] ?? '',
                    // 「选填」（适用于 2024-11 及之前开通微信支付的老商户）微信平台公钥证书路径，强烈建议 php-fpm 模式下配置此参数
                    // 「必填」微信支付公钥路径，key 填写形如 PUB_KEY_ID_0000000000000024101100397200000006 的公钥id，见 https://pay.weixin.qq.com/doc/v3/merchant/4013053249
                    'wechat_public_cert_path' => $wechatTerminalConfig['wechat_public_serial'] ? [
                        $wechatTerminalConfig['wechat_public_serial'] => $wechatTerminalConfig['wechat_public_cert'],
                    ] : [],
                    // 「选填」默认为正常模式。可选为： MODE_NORMAL, MODE_SERVICE
                    'mode' => ($wechatTerminalConfig['sub_mch_id'] ?? '') ? Pay::MODE_SERVICE : Pay::MODE_NORMAL,
                ]
            ],
            'unipay' => [
                'default' => [
                    // 「必填」商户号
                    'mch_id' => '777290058167151',
                    // 「选填」商户密钥：为银联条码支付综合前置平台配置：https://up.95516.com/open/openapi?code=unionpay
                    'mch_secret_key' => '979da4cfccbae7923641daa5dd7047c2',
                    // 「必填」商户公私钥
                    'mch_cert_path' => __DIR__.'/Cert/unipayAppCert.pfx',
                    // 「必填」商户公私钥密码
                    'mch_cert_password' => '000000',
                    // 「必填」银联公钥证书路径
                    'unipay_public_cert_path' => __DIR__.'/Cert/unipayCertPublicKey.cer',
                    // 「必填」
                    'return_url' => 'https://yansongda.cn/unipay/return',
                    // 「必填」
                    'notify_url' => 'https://yansongda.cn/unipay/notify',
                    'mode' => Pay::MODE_NORMAL,
                ],
            ],
            'douyin' => [
                'default' => [
                    // 「选填」商户号
                    // 抖音开放平台 --> 应用详情 --> 支付信息 --> 产品管理 --> 商户号
                    'mch_id' => '',
                    // 「必填」支付 Token，用于支付回调签名
                    // 抖音开放平台 --> 应用详情 --> 支付信息 --> 支付设置 --> Token(令牌)
                    'mch_secret_token' => '',
                    // 「必填」支付 SALT，用于支付签名
                    // 抖音开放平台 --> 应用详情 --> 支付信息 --> 支付设置 --> SALT
                    'mch_secret_salt' => '',
                    // 「必填」小程序 app_id
                    // 抖音开放平台 --> 应用详情 --> 支付信息 --> 支付设置 --> 小程序appid
                    'mini_app_id' => '',
                    // 「选填」抖音开放平台服务商id
                    'thirdparty_id' => '',
                    // 「选填」抖音支付回调地址
                    'notify_url' => '',
                ],
            ],
            'jsb' => [
                'default' => [
                    // 服务代码
                    'svr_code' => '',
                    // 「必填」合作商ID
                    'partner_id' => '',
                    // 「必填」公私钥对编号
                    'public_key_code' => '00',
                    // 「必填」商户私钥(加密签名)
                    'mch_secret_cert_path' => '',
                    // 「必填」商户公钥证书路径(提供江苏银行进行验证签名用)
                    'mch_public_cert_path' => '',
                    // 「必填」江苏银行的公钥(用于解密江苏银行返回的数据)
                    'jsb_public_cert_path' => '',
                    // 支付通知地址
                    'notify_url' => '',
                    // 「选填」默认为正常模式。可选为： MODE_NORMAL:正式环境, MODE_SANDBOX:测试环境
                    'mode' => Pay::MODE_NORMAL,
                ],
            ],
            'logger' => [
                'enable'    => true,
                'file'      => app()->getRuntimePath() . 'pay_logs/' . date('Ymd', $time) . '/' . date('H', $time) . '_pay.log',
                // 建议生产环境等级调整为 info，开发环境为 debug
                'level'     => env('app_debug') ? 'debug' : 'info',
                // optional, 可选 daily.
                'type'      => 'single',
                // optional, 当 type 为 daily 时有效，默认 30 天
                'max_file'  => 30,
            ],
            'http' => [ // optional
                'timeout' => 5.0,
                'connect_timeout' => 5.0,
                // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
            ],
        ];
    }
}