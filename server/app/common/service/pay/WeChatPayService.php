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


namespace app\common\service\pay;


use app\common\enum\PayEnum;
use app\common\model\PayConfig;
use app\common\service\pay\wechat\WechatPayV2Default;
use app\common\service\pay\wechat\WechatPayV3Default;


/**
 * 微信支付
 * Class WeChatPayService
 * @package app\common\server
 */
class WeChatPayService extends BasePayService
{


    /**
     * easyWeChat实例
     * @var
     */
    protected $pay;
    
    
    /**
     * 初始化微信配置
     * @param $terminal //用户终端
     * @param null $userId //用户id(获取授权openid)
     * @throws \Exception
     */
    public function __construct($terminal, $userId = null)
    {
        $config = PayConfig::where('pay_way', PayEnum::WECHAT_PAY)->findOrEmpty()->toArray();
        
        if (empty($config['id'])) {
            throw new \Exception('请配置好支付设置');
        }
        
        $initData = [
            'terminal'  => $terminal,
            'config'    => $config,
            'pay_way'   => PayEnum::WECHAT_PAY,
            'user_id'   => $userId
        ];
        
        switch ($config['config']['merchant_type'] . '-' . $config['config']['interface_version']) {
            // 普通商户 v2
            case 'ordinary_merchant-v2':
                $this->pay = new WechatPayV2Default($initData);
                break;
            // 普通商户 v3
            case 'ordinary_merchant-v3':
            // 服务商子商户 v3
            case 'partner_sub_merchant-v3':
                $this->pay = new WechatPayV3Default($initData);
                break;
            // 服务商 v3
            case 'partner_merchant-v3':
                throw new \Exception('不支持 服务商的支付方式');
                break;
            default:
                throw new \Exception('不支持的支付方式');
        }
    }
    
    function realPay()
    {
        return $this->pay;
    }
    
    function pay($form, $order)
    {
        return $this->pay->pay($form, $order);
    }


    /**
     * @notes 支付回调
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     * @author 段誉
     * @date 2021/8/13 14:19
     */
    public function notify()
    {
        return $this->pay->payNotify();
    }

    /**
     * @notes 退款
     * @param $data //微信订单号、商户退款单号、订单金额、退款金额、其他参数
     * @return bool
     * @author 段誉
     * @date 2021/8/4 14:57
     */
    public function refund($data)
    {
        $this->pay->set_params([ 'refund_data' => $data ]);
        
        return $this->pay->refund();
    }
    
    function queryRefund($refund)
    {
        $this->pay->set_params([ 'query_refund_data' => $refund ]);
        
        return $this->pay->queryRefund();
    }

}