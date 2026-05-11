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

// | Author: LikeShopTeam-段誉
// +----------------------------------------------------------------------


namespace app\common\service\pay;


use app\common\enum\PayEnum;
use app\common\model\PayConfig;
use app\common\service\pay\alipay\AlipayV2Default;
use app\common\service\pay\alipay\AlipayV2Service;
use app\common\service\pay\alipay\AlipayV3Default;
use app\common\service\pay\alipay\AlipayV3Service;

/**
 * 支付宝支付
 * Class AliPayService
 * @package app\common\server
 */
class AliPayService extends BasePayService
{
    /**
     * 支付实例
     * @var
     */
    protected $pay;

    /**
     * 初始化设置
     * AliPayService constructor.
     * @throws \Exception
     */
    public function __construct($terminal = null)
    {
        $config = (new PayConfig())->where(['pay_way' => PayEnum::ALI_PAY])->findOrEmpty()->toArray();
        
        if (empty($config['id'])) {
            throw new \Exception('请配置好支付设置');
        }
        
        $initData = [
            'terminal'  => $terminal,
            'config'    => $config,
            'pay_way'   => PayEnum::ALI_PAY,
        ];
        
        switch ($config['config']['merchant_type'] . '-' . $config['config']['mode']) {
            // 普通商户 普通加密
            case 'ordinary_merchant-normal_mode':
                $this->pay = new AlipayV2Default($initData);
                break;
            // 服务商 普通加密
            case 'service-normal_mode':
                $this->pay = new AlipayV2Service($initData);
                break;
            // 普通商户 证书加密
            case 'normal-v3':
                $this->pay = new AlipayV3Default($initData);
                break;
            // 服务商 证书加密
            case 'service-v3':
                $this->pay = new AlipayV3Service($initData);
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
    
    function notify($data)
    {
        $this->pay->set_params([ 'notify_data' => $data ]);
        
        return $this->pay->payNotify();
    }
    
    function checkPay($orderSn)
    {
        $this->pay->set_params([ 'query_pay_data' => [ 'order_sn' => $orderSn ] ]);
        
        return $this->pay->queryPay();
    }
    
    function refund($orderSn, $orderAmount, $outRequestNo)
    {
        $this->pay->set_params([
            'refund_data' => [
                'order_sn'          => $orderSn,
                'order_amount'      => $orderAmount,
                'out_request_no'    => $outRequestNo,
            ]
        ]);
        
        return $this->pay->refund();
    }
    
    function queryRefund($orderSn, $refundSn)
    {
        $this->pay->set_params([
            'query_refund_data' => [
                'order_sn'          => $orderSn,
                'refund_sn'         => $refundSn,
            ]
        ]);
        
        return $this->pay->queryRefund();
    }
}

