<?php

namespace app\common\service\pay\wechat;

use app\common\enum\PayEnum;
use app\common\model\UserAuth;
use app\common\service\pay\base\BasePayService;
use app\common\service\pay\YansongdaPay;
use app\common\service\WeChatConfigService;
use EasyWeChat\Factory;
use think\facade\Log;
use think\helper\Str;
use WeChatPay\Builder;
use WeChatPay\BuilderChainable;
use WeChatPay\Crypto\AesGcm;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Formatter;
use WeChatPay\Util\PemUtil;
use Yansongda\Pay\Pay;

/**
 * @notes 微信支付 v3
 * author lbzy
 * @datetime 2024-11-11 13:55:30
 * @class WechatPayV3Default
 * @package app\common\service\pay\wechat
 */
class WechatPayV3Default extends BasePayService
{
    protected array $fromArray = [
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
    
    /**
     * 授权信息
     * @var UserAuth|array|\think\Model
     */
    protected $auth;
    
    protected array|null $wechatTerminalConfig = null;
    
    /**
     * 支付实例
     * @var
     */
    protected $pay;
    
    function __construct($params)
    {
        parent::__construct($params);
        
        if ($this->user_id) {
            $this->auth = UserAuth::where([ 'user_id' => $this->user_id, 'terminal' => $this->terminal ])->findOrEmpty();
        }
        
        $this->wechatTerminalConfig = WeChatConfigService::getWechatConfigByTerminal($this->terminal);
        
        Pay::config(YansongdaPay::getWechatConfig($this->wechatTerminalConfig));
    }
    
    
    
    /**
     * @notes notes
     * https://pay.weixin.qq.com/docs/merchant/apis/mini-program-payment/mini-prepay.html
     * @return array|false
     * @throws \Exception
     * @author lbzy
     * @datetime 2024-11-18 15:48:39
     */
    function payMnp() : bool|array
    {
        if ($this->auth->isEmpty()) {
            if (!empty($this->payOrder['openid'])) {
                $this->auth = ['openid' => $this->payOrder['openid']];
            } else {
                throw new \Exception('获取授权信息失败');
            }
        }
        
        $order = $this->payOrder;
        
        $suffix = mb_substr(time(), -4);
        $out_trade_no = $order['sn'] . 'JSAPI' . $this->terminal . $suffix;
        
        $result = Pay::wechat()->mini([
            'out_trade_no'  => $out_trade_no,
            'description'   => $this->fromArray[$this->payAttach]['body'],
            'amount'        => [
                'total'         => (int) bcmul($order['order_amount'], 100),
                'currency'      => 'CNY',
            ],
            'payer'         => [
                'openid'        => $this->auth['openid'],
            ],
            'attach'        => $this->fromArray[$this->payAttach]['attach'],
        ]);
        
        if (! isset($result['paySign'])) {
            throw new \Exception($result['message'] ?? '发起支付失败');
        }
        
        return $result->toArray();
    }
    
    function payOa()
    {
        return $this->payMnp();
    }
    
    function payIos()
    {
        $order = $this->payOrder;
        
        $suffix = mb_substr(time(), -4);
        $out_trade_no = $order['sn'] . 'JSAPI' . $this->terminal . $suffix;
        
        $result = Pay::wechat()->app([
            'out_trade_no' => $out_trade_no,
            'description' => $this->fromArray[$this->payAttach]['body'],
            'amount'       => [
                'total'    => (int) bcmul($order['order_amount'], 100),
                'currency' => 'CNY'
            ],
            'attach'    => $this->fromArray[$this->payAttach]['attach'],
        ]);
        
        if (! isset($result['paySign'])) {
            throw new \Exception($result['message'] ?? '发起支付失败');
        }
        
        return $result->toArray();
    }
    
    function payAndroid()
    {
        return $this->payIos();
    }
    
    function payH5()
    {
        $order = $this->payOrder;
        
        $suffix = mb_substr(time(), -4);
        $out_trade_no = $order['sn'] . 'JSAPI' . $this->terminal . $suffix;
        $result = Pay::wechat()->h5([
            'out_trade_no' => $out_trade_no,
            'description'  => $this->fromArray[$this->payAttach]['body'],
            'amount'       => [
                'total'    => (int) bcmul($order['order_amount'], 100),
                'currency' => 'CNY'
            ],
            'attach'        => $this->fromArray[$this->payAttach]['attach'],
            'scene_info'    => [
                'payer_client_ip'   => request()->ip(),
                'h5_info' => [
                    'type' => 'Wap',
                ]
            ],
        ]);
        
        if (! isset($result['h5_url'])) {
            throw new \Exception($result['message'] ?? '发起支付失败');
        }

        $redirect_url = request()->domain() . '/mobile/pages/payment_result/payment_result?order_id='.$this->payOrder['id'].'&from='.$this->payAttach;
        $redirect_url = urlencode($redirect_url);
        
        return $result['h5_url'] . '&redirect_url=' . $redirect_url;
    }
    
    function payPc()
    {
        $order = $this->payOrder;
        
        $suffix = mb_substr(time(), -4);
        $out_trade_no = $order['sn'] . 'JSAPI' . $this->terminal . $suffix;
        
        $result = Pay::wechat()->scan([
            'out_trade_no' => $out_trade_no,
            'description'  => $this->fromArray[$this->payAttach]['body'],
            'amount'       => [
                'total'    => (int) bcmul($order['order_amount'], 100),
                'currency' => 'CNY'
            ],
            'attach'        => $this->fromArray[$this->payAttach]['attach'],
        ]);
        
        if (! isset($result['code_url'])) {
            throw new \Exception($result['message'] ?? '发起支付失败');
        }
        
        return [
            'code_url'      => $result['code_url'],
            'order_amount'  => $order['order_amount'],
        ];
    }
    
    function payNotify()
    {
        try {
            $data = Pay::wechat()->callback()->toArray()['resource']['ciphertext'] ?? [];
            
            // Log::write($data, 'payNotifyData');
            
            if (! is_array($data)) {
                return $data;
            }
            
            if ($data['trade_state'] == 'SUCCESS') {
                $extra = [
                    'transaction_id'    => $data['transaction_id'],
                ];
                $this->paySuccess([
                    'extra'             => $extra,
                    'from'              => $data['attach'],
                    'out_trade_no'      => mb_substr($data['out_trade_no'], 0, 18),
                    'transaction_id'    => $data['transaction_id'],
                ]);
                return Pay::wechat()->success();
            }
            
            return true;
        } catch(\Throwable $e) {
            Log::write($e->__toString(), 'payNotifyError');
            return false;
        }
    }
    
    function queryPay()
    {
    
    }
    
    /**
     * @notes https://pay.weixin.qq.com/doc/v3/merchant/4012556453
     * @return
     * @author lbzy
     * @datetime 2024-11-21 09:23:49
     */
    function refund()
    {
        $data = $this->params['refund_data'];
        
        $order = [
            'transaction_id'    => $data['transaction_id'],
            'out_refund_no'     => $data['refund_sn'],
            'amount'            => [
                'total'             => (int) bcmul($data['total_fee'], 100),
                'currency'          => 'CNY',
                'refund'            => (int) bcmul($data['refund_fee'], 100),
            ],
            // '_action' => 'jsapi', // jsapi 退款，默认
            // '_action' => 'app', // app 退款
            // '_action' => 'combine', // 合单退款
            // '_action' => 'h5', // h5 退款
            // '_action' => 'mini', // 小程序退款
            // '_action' => 'native', // native 退款
        ];
        
        $result = Pay::wechat()->refund($order)->all();
        
        if (isset($result['status']) && in_array($result['status'], [ 'SUCCESS', 'PROCESSING' ]) ) {
            return true;
        }
        
        Log::write($result, 'wechatRefundError');
        
        $this->setStatus(false, '发起退款失败:' . ($result['message'] ?? ''));
        
        return false;
    }
    
    function queryRefund()
    {
        try {
            $refund = $this->params['query_refund_data'];
            
            $order = [
                'out_refund_no' => $refund['sn'],
                '_action' => 'refund',
                // '_action' => 'refund_jsapi', // 查询 jsapi 退款订单，默认
                // '_action' => 'refund_app', // 查询 App 退款订单
                // '_action' => 'refund_h5', // 查询 H5 退款订单
                // '_action' => 'refund_mini', // 查询小程序退款订单
                // '_action' => 'refund_native', // 查询 Native 退款订单
                // '_action' => 'refund_combine', // 查询合单退款订单
            ];
            
            $result = Pay::wechat()->query($order)->all();
            
            if (isset($result['status']) && $result['status'] == 'SUCCESS') {
                return true;
            }
            
            if (isset($result['status']) && in_array($result['status'], [ 'CLOSED', 'ABNORMAL' ]) ) {
                Log::write($result, 'wechatQueryRefundError');
                return false;
            }
            
            // if (isset($result['status']) && $result['status'] == 'PROCESSING') {
            //     return null;
            // }
            
            return $result;
            return true;
        } catch(\Throwable $e) {
            Log::write($e->__toString(), 'wechatQueryRefundError');
            return false;
        }
        
    }
    
}