<?php

namespace app\common\service\pay\wechat;

use app\common\enum\AfterSaleLogEnum;
use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\enum\UserTerminalEnum;
use app\common\logic\PayNotifyLogic;
use app\common\model\IntegralOrder;
use app\common\model\Order;
use app\common\model\RechargeOrder;
use app\common\model\UserAuth;
use app\common\service\after_sale\AfterSaleService;
use app\common\service\pay\base\BasePayService;
use app\common\service\WeChatConfigService;
use EasyWeChat\Factory;
use EasyWeChat\Payment\Application;
use think\facade\Log;

/**
 * @notes 微信支付 v2
 * author lbzy
 * @datetime 2024-11-11 13:55:22
 * @class WechatPayV2Default
 * @package app\common\service\pay\wechat
 */
class WechatPayV2Default extends BasePayService
{
    
    /**
     * 授权信息
     * @var UserAuth|array|\think\Model
     */
    protected $auth;
    
    
    /**
     * easyWeChat实例
     * @var
     */
    protected $pay;
    
    function __construct($params)
    {
        parent::__construct($params);
        
        $this->pay = Factory::payment(WeChatConfigService::getWechatConfigByTerminal($this->terminal));
        
        if ($this->user_id) {
            $this->auth = UserAuth::where([ 'user_id' => $this->user_id, 'terminal' => $this->terminal ])->findOrEmpty();
        }
    }
    
    function payMnp()
    {
        if ($this->auth->isEmpty()) {
            if (!empty($this->payOrder['openid'])) {
                $this->auth = ['openid' => $this->payOrder['openid']];
            } else {
                throw new \Exception('获取授权信息失败');
            }
        }
        
        $result = $this->pay->order->unify($this->getAttributes($this->payAttach, $this->payOrder));
        
        $this->checkResultFail($result);
        
        return $this->pay->jssdk->bridgeConfig($result['prepay_id'], false);
    }
    
    function payOa()
    {
        return $this->payMnp();
    }
    
    function payIos()
    {
        $result = $this->pay->order->unify($this->getAttributes($this->payAttach, $this->payOrder));
        
        $this->checkResultFail($result);
        
        return $this->pay->jssdk->appConfig($result['prepay_id']);
    }
    
    function payAndroid()
    {
        return $this->payIos();
    }
    
    function payH5()
    {
        $result = $this->pay->order->unify($this->getAttributes($this->payAttach, $this->payOrder));
        
        $this->checkResultFail($result);

        $redirect_url = request()->domain() . '/mobile/pages/payment_result/payment_result?order_id='.$this->payOrder['id'].'&from='.$this->payAttach;
        $redirect_url = urlencode($redirect_url);
        
        return $result['mweb_url'] . '&redirect_url=' . $redirect_url;
    }
    
    function payPc()
    {
        $result = $this->pay->order->unify($this->getAttributes($this->payAttach, $this->payOrder));
        $this->checkResultFail($result);
        // 返回信息由前端生成支付二维码
        return [
            'code_url' => $result['code_url'],
            'order_amount' => $this->payOrder['order_amount']
        ];
    }
    
    function getAttributes($from, $order): array
    {
        $fromArray = [
            'order' => [
                'body'      => '商品',
                'attach'    => 'order',
            ],
            'recharge' => [
                'body'      => '充值',
                'attach'    => 'recharge',
            ],
            'integral' => [
                'body'      => '积分商城',
                'attach'    => 'integral',
            ],
        ];
        
        $terminalTradeArray = [
            UserTerminalEnum::ANDROID   => 'APP',
            UserTerminalEnum::IOS       => 'APP',
            UserTerminalEnum::H5        => 'MWEB',
            UserTerminalEnum::PC        => 'NATIVE',
        ];
        
        $attributes = [
            'trade_type'    => $terminalTradeArray[$this->terminal] ?? 'JSAPI',
            'body'          => $fromArray[$from]['body'],
            'total_fee'     => (int) bcmul($order['order_amount'], 100), // 单位：分
            'openid'        => $this->auth['openid'],
            'attach'        => $fromArray[$from]['attach'],
        ];
        
        //NATIVE模式设置
        if ($this->terminal == UserTerminalEnum::PC) {
            $attributes['product_id'] = $order['sn'];
        }
        
        //修改微信统一下单,订单编号 -> 支付回调时截取前面的单号 18个
        //修改原因:回调时使用了不同的回调地址,导致跨客户端支付时(例如小程序,公众号)可能出现201,商户订单号重复错误
        $suffix = mb_substr(time(), -4);
        $attributes['out_trade_no'] = $order['sn'] . $attributes['trade_type'] . $this->terminal . $suffix;
        
        return $attributes;
    }
    
    function checkResultFail($result) : void
    {
        if ($result['return_code'] != 'SUCCESS' || $result['result_code'] != 'SUCCESS') {
            if (isset($result['return_code']) && $result['return_code'] == 'FAIL') {
                throw new \Exception($result['return_msg']);
            }
            if (isset($result['err_code_des'])) {
                throw new \Exception($result['err_code_des']);
            }
            throw new \Exception('未知原因');
        }
    }
    
    function payNotify()
    {
        $app = new Application(WeChatConfigService::getWechatConfigByTerminal($this->terminal));
        $response = $app->handlePaidNotify(function ($message, $fail) {
            
            if ($message['return_code'] !== 'SUCCESS') {
                return $fail('通信失败');
            }
            
            // 用户是否支付成功
            if ($message['result_code'] === 'SUCCESS') {
                $extra['transaction_id'] = $message['transaction_id'];
                
                $message['out_trade_no'] = mb_substr($message['out_trade_no'], 0, 18);
                
                $this->paySuccess([
                    'extra'             => $extra,
                    'from'              => $message['attach'],
                    'out_trade_no'      => $message['out_trade_no'],
                    'transaction_id'    => $message['transaction_id'],
                ]);
                
            } elseif ($message['result_code'] === 'FAIL') {
                // 用户支付失败
                
            }
            return true; // 返回处理完成
            
        });
        
        return $response->send();
    }
    
    function queryPay()
    {
    
    }
    
    
    function refund()
    {
        $data = $this->params['refund_data'];
        
        $result = $this->pay->refund->byTransactionId(
            $data['transaction_id'],
            $data['refund_sn'],
            (int) bcmul($data['total_fee'], 100),
            (int) bcmul($data['refund_fee'], 100),
        );
        
        if (($result['return_code'] ?? '') == 'SUCCESS' && ($result['result_code'] ?? '') == 'SUCCESS') {
            return true;
        }
        
        Log::write($result, 'wechatRefundError');
        
        $this->setStatus(false, $result['return_msg'] ?? $result['err_code_des'] ?? '发起退款失败');
        
        return false;
    }
    
    function queryRefund()
    {
        $refund = $this->params['query_refund_data'];
        $result = $this->pay->refund->queryByOutRefundNumber($refund['sn']);
        
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS' && $result['refund_status_0'] == 'SUCCESS') {
            // 退款成功
            return true;
        }
        
        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS' && $result['refund_status_0'] == 'REFUNDCLOSE') {
            Log::write($result, 'wechatQueryRefundError');
            // 退款失败
            return false;
        }
        
        return $result;
    }
    
}