<?php

namespace app\common\service\pay\alipay;

use Alipay\EasySDK\Kernel\Config;
use Alipay\EasySDK\Kernel\Factory;
use app\common\enum\AfterSaleLogEnum;
use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\logic\PayNotifyLogic;
use app\common\model\IntegralOrder;
use app\common\model\Order;
use app\common\model\RechargeOrder;
use app\common\service\after_sale\AfterSaleService;
use app\common\service\pay\base\BasePayService;
use think\facade\Log;

/**
 * @notes 支付宝v2 默认
 * author lbzy
 * @datetime 2024-11-11 13:47:50
 * @class AlipayV2
 * @package app\common\service\pay\alipay
 */
class AlipayV2Default extends BasePayService
{
    function __construct($params)
    {
        parent::__construct($params);
        
        //初始化支付配置
        Factory::setOptions($this->getOptions());
        $this->pay = Factory::payment();
    }
    
    function getOptions()
    {
        $config = $this->config;
        
        $options = new Config();
        $options->protocol = 'https';
        $options->gatewayHost = 'openapi.alipay.com';
        // $options->gatewayHost = 'openapi.alipaydev.com'; //测试沙箱地址
        $options->signType = 'RSA2';
        $options->appId = $config['config']['app_id'] ?? '';
        // 应用私钥
        $options->merchantPrivateKey = $config['config']['private_key'] ?? '';
        //支付宝公钥
        $options->alipayPublicKey = $config['config']['ali_public_key'] ?? '';
        //回调地址
        $options->notifyUrl = (string)url('pay/aliNotify', [], false, true);
        
        return $options;
    }
    
    function payMnp(): string
    {
        return $this->payH5();
    }
    
    function payOa(): string
    {
        return $this->payH5();
    }
    
    function payIos(): string
    {
        $order  = $this->payOrder;
        
        $result = $this->pay->app()->optional('passback_params', $this->payAttach)->pay(
            $order['sn'],
            $order['sn'],
            $order['order_amount']
        );
        
        return $result->body;
    }
    
    function payAndroid(): string
    {
        return $this->payIos();
    }
    
    function payH5(): string
    {
        $order  = $this->payOrder;
        $attach = $this->payAttach;

        $url = request()->domain() . '/mobile/pages/payment_result/payment_result?order_id='.$this->payOrder['id'].'&from='.$this->payAttach;

        $result = $this->pay->wap()->optional('passback_params', $attach)->pay(
            '订单:' . $order['sn'],
            $order['sn'],
            $order['order_amount'],
            $url,
            $url
        );
        
        return $result->body;
    }
    
    function payPc(): string
    {
        $domain = request()->domain();
//        $order  = $this->params['payData']['order'];
//        $attach = $this->params['payData']['attach'];
        $order  = $this->payOrder;
        $attach = $this->payAttach;
        
        $result = $this->pay->page()->optional('passback_params', $attach)->pay(
            '订单:' . $order['sn'],
            $order['sn'],
            $order['order_amount'],
            $domain . '/pc/user/order'
        );
        
        return $result->body;
    }
    
    function payNotify(): bool
    {
        $data = $this->params['notify_data'];
        
        try {
            $verify = $this->pay->common()->verifyNotify($data);
            if (false === $verify) {
                throw new \Exception('异步通知验签失败');
            }
            if (!in_array($data['trade_status'], ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
                return true;
            }
            $extra['transaction_id'] = $data['trade_no'];
            //验证订单是否已支付
            $this->paySuccess([
                'extra'             => $extra,
                'from'              => $data['passback_params'],
                'out_trade_no'      => $data['out_trade_no'],
                'transaction_id'    => $data['trade_no'],
            ]);
            
            return true;
        } catch (\Exception $e) {
            $record = [
                __CLASS__,
                __FUNCTION__,
                $e->getFile(),
                $e->getLine(),
                $e->getMessage()
            ];
            Log::write(implode('-', $record));
            $this->setStatus(false, $e->getMessage());
            return false;
        }
    }
    
    function queryPay()
    {
        return $this->pay->common()->query($this->params['query_pay_data']['order_sn']);
    }
    
    
    function refund(): \Alipay\EasySDK\Payment\Common\Models\AlipayTradeRefundResponse
    {
        return $this->pay->common()
            ->optional('out_request_no', $this->params['refund_data']['out_request_no'])
            ->refund($this->params['refund_data']['order_sn'], $this->params['refund_data']['order_amount']);
    }
    
    function queryRefund()
    {
        return $this->pay->common()
            ->queryRefund($this->params['query_refund_data']['order_sn'], $this->params['query_refund_data']['refund_sn']);
    }
    
}