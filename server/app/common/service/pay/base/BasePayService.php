<?php

namespace app\common\service\pay\base;

use app\common\enum\AfterSaleLogEnum;
use app\common\enum\OrderEnum;
use app\common\enum\PayEnum;
use app\common\enum\UserTerminalEnum;
use app\common\logic\BaseLogic;
use app\common\logic\PayNotifyLogic;
use app\common\model\IntegralOrder;
use app\common\model\Order;
use app\common\model\RechargeOrder;
use app\common\service\after_sale\AfterSaleService;
use Psr\Http\Message\MessageInterface;
use think\facade\Log;
use Yansongda\Artful\Exception\InvalidResponseException;
use Yansongda\Supports\Collection;

/**
 * @notes notes
 * author lbzy
 * @datetime 2024-11-11 14:15:49
 * @class BasePayService
 * @package app\common\service\pay\base
 */
abstract class BasePayService
{
    
    use StatusTrait;
    
    protected array $params = [];
    
    protected int|null $user_id = null;
    
    protected int|null $terminal;
    
    protected array|null $config;
    
    protected int|null $pay_way;
    
    /**
     * @var
     */
    protected $payOrder;
    
    protected $payAttach;
    
    function __construct(array $params)
    {
        $this->params = $params;
        if (isset($params['terminal'])) {
            $this->terminal = $params['terminal'];
        }
        
        if (isset($params['user_id'])) {
            $this->user_id = $params['user_id'];
        }
        
        $this->pay_way  = $params['pay_way'];
        
        $this->config   = $params['config'] ?? [];
    }
    
    function set_params(array $params): void
    {
        $this->params = array_merge($this->params, $params);
    }
    
    function pay($from, $order) : bool|array
    {
        $this->payOrder = $order;
        $this->payAttach = $from;
        
        try {
            switch ($this->params['terminal']) {
                case UserTerminalEnum::PC:
                    $result = $this->payPc();
                    break;
                case UserTerminalEnum::IOS:
                    $result = $this->payIos();
                    break;
                case UserTerminalEnum::ANDROID:
                    $result = $this->payAndroid();
                    break;
                case UserTerminalEnum::H5:
                    $result = $this->payH5();
                    break;
                case UserTerminalEnum::WECHAT_MMP:
                    $result = $this->payMnp();
                break;
                case UserTerminalEnum::WECHAT_OA:
                    $result = $this->payOa();
                    break;
                default:
                    throw new \Exception('支付方式错误');
            }
            return [
                'config' => $result,
                'pay_way' => $this->pay_way,
            ];
        }  catch (\Throwable $e) {
            if ($e instanceof InvalidResponseException) {
                if($e->response instanceof Collection || $e->response instanceof MessageInterface) {
                    $this->setStatus(false, $e->response->get('message') ? : $e->getMessage());
                    return false;
                }
            }
            Log::write($e->__toString(), 'payError');
            $this->setStatus(false, $e->getMessage());
            return false;
        }
    }
    
    protected function paySuccess(array $params)
    {
        $from           = $params['from'] ?? '';
        $out_trade_no   = $params['out_trade_no'] ?? '';
        $transaction_id = $params['transaction_id'] ?? '';
        $extra          = $params['extra'] ?? [];
        
        switch ($from) {
            case 'order':
                $order = Order::where([ 'sn' => $out_trade_no ])->findOrEmpty();
                if (!$order || $order['pay_status'] >= PayEnum::ISPAID) {
                    return true;
                }
                
                //特殊情况：用户在前端支付成功的情况下，调用回调接口之前，订单被关闭
                if ($order['order_status'] == OrderEnum::STATUS_CLOSE) {
                    //更新订单支付状态为已支付
                    Order::update([ 'pay_status' => PayEnum::ISPAID, 'transaction_id' => $transaction_id ],[
                        'id' => $order['id']
                    ]);
                    
                    //发起售后
                    AfterSaleService::orderRefund([
                        'order_id' => $order['id'],
                        'scene' =>  AfterSaleLogEnum::ORDER_CLOSE
                    ]);
                    
                    return true;
                }
                
                PayNotifyLogic::handle('order', $out_trade_no, $extra);
                break;
            case 'recharge':
                $order = RechargeOrder::where(['sn' => $out_trade_no ])->findOrEmpty();
                if($order->isEmpty() || $order->pay_status == PayEnum::ISPAID) {
                    return true;
                }
                PayNotifyLogic::handle('recharge', $out_trade_no, $extra);
                break;
            case 'integral':
                $order = IntegralOrder::where(['sn' => $out_trade_no ])->findOrEmpty();
                if($order->isEmpty() || $order['pay_status'] != PayEnum::UNPAID) {
                    return true;
                }
                PayNotifyLogic::handle('integral', $out_trade_no, $extra);
                break;
        }
    }
    
    abstract public function payMnp();
    abstract public function payOa();
    abstract public function payIos();
    abstract public function payAndroid();
    abstract public function payH5();
    abstract public function payPc();
    
    abstract public function payNotify();
    abstract public function queryPay();
    
    abstract public function refund();
    abstract public function queryRefund();
}