<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\adminapi\logic\withdraw;



use app\common\enum\AccountLogEnum;
use app\common\enum\NoticeEnum;
use app\common\enum\WithdrawEnum;
use app\common\logic\AccountLogLogic;
use app\common\model\Notice;
use app\common\model\UserAuth;
use app\common\model\WithdrawApply;
use app\common\service\ConfigService;
use app\common\service\WeChatConfigService;

/**
 * 功能: 商家转账到零钱
 * 用途：商户可以通过该接口同时向多个用户微信零钱进行转账操作。
 * 证书：需要
 * 请求URL：https://api.mch.weixin.qq.com/v3/transfer/batches
 * 失败后一定要用【原来的商户订单号】去重试，不然有可能存在重复支付的风险！！！
 * 转账批次单中涉及金额的字段单位为“分”
 * 成功受理商家转账请求后，可调用《商家明细单号查询明细单》接口来判断转账明细列表状态
 */
class WechatMerchantTransferLogic
{
    /**
     * @notes 商家转账到零钱
     * @param $withdrawApply
     * @return bool
     * @throws \Exception
     * @author ljj
     * @date 2022/9/27 4:40 下午
     */
    public static function transfer($withdrawApply, $params = [])
    {
        // 用户授权信息
        $userAuth = UserAuth::where(['user_id'=>$withdrawApply['user_id'],'terminal'=>$withdrawApply['terminal']])->findOrEmpty();
        if($userAuth->isEmpty()) {
            throw new \Exception('查询不到该用户的微信授权信息');
        }
        // 获取微信配置
        $config = WeChatConfigService::getWechatConfigByTerminal($userAuth->terminal);
        //请求URL
        $url = 'https://api.mch.weixin.qq.com/v3/fund-app/mch-transfer/transfer-bills';
        //请求方式
        $http_method = 'POST';
        //请求参数
        $data = [
            'appid' => $config['app_id'],
            'out_bill_no' => $withdrawApply['sn'],
            'transfer_scene_id' => '1005',
            'transfer_remark' => '提现',
            'openid' => $userAuth['openid'],
            'transfer_amount' => intval(bcmul($withdrawApply['left_money'], 100)),
            'transfer_scene_report_infos' => [
                [ 'info_type' => '岗位类型', 'info_content' => '邀请者' ],
                [ 'info_type' => '报酬说明', 'info_content' => '邀请奖励提现' ],
            ],
        ];
        if ($withdrawApply['left_money'] >= 2000) {
            if (empty($withdrawApply['real_name'])) {
                throw new \Exception('转账金额 >= 2000元，收款用户真实姓名必填');
            }
            $data['transfer_detail_list'][0]['user_name'] = self::getEncrypt($withdrawApply['real_name'],$config);
        }

        $token  = self::token($url,$http_method,$data,$config);//获取token
        $result = self::https_request($url,json_encode($data),$token);//发送请求
        $result_arr = json_decode($result,true);

        if(!isset($result_arr['create_time'])) {//批次受理失败
            if (strpos($result_arr['message'],"产品权限异常") !== false) {
                throw new \Exception('产品权限异常 -  请在商户平台-商家转账产品设置中开通产品权限');
            }
            throw new \Exception($result_arr['message'] ?? $result['fail_reason'] ?? '零钱提现请求失败');
        }

        //批次受理成功，更新提现申请单为提现中状态
        WithdrawApply::update([
            'status' => WithdrawEnum::STATUS_ING,
            'audit_remark' => $params['audit_remark'] ?? '',
            'pay_desc' => $result,
        ],['id'=>$withdrawApply['id']]);

        return true;
    }

    /**
     * @notes 签名生成
     * @param $url
     * @param $http_method
     * @param $data
     * @param $config
     * @return string
     * @author ljj
     * @date 2022/9/27 4:14 下午
     */
    public static function token($url,$http_method,$data,$config)
    {
        $timestamp   = time();//请求时间戳
        $url_parts   = parse_url($url);//获取请求的绝对URL
        $nonce       = $timestamp.mt_rand(10000,99999);//请求随机串
        $body        = empty($data) ? '' : json_encode((object)$data);//请求报文主体
        $stream_opts = [
            "ssl" => [
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ]
        ];

        $apiclient_cert_arr = openssl_x509_parse(file_get_contents($config['cert_path'],false, stream_context_create($stream_opts)));
        $serial_no          = $apiclient_cert_arr['serialNumberHex'];//证书序列号
        $mch_private_key    = file_get_contents($config['key_path'],false, stream_context_create($stream_opts));//密钥
        $merchant_id = $config['mch_id'];//商户id
        $canonical_url = ($url_parts['path'] . (!empty($url_parts['query']) ? "?${url_parts['query']}" : ""));
        $message = $http_method."\n".
            $canonical_url."\n".
            $timestamp."\n".
            $nonce."\n".
            $body."\n";
        openssl_sign($message, $raw_sign, $mch_private_key, 'sha256WithRSAEncryption');
        $sign = base64_encode($raw_sign);//签名
        $schema = 'WECHATPAY2-SHA256-RSA2048';
        $token = sprintf('mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $merchant_id, $nonce, $timestamp, $serial_no, $sign);//微信返回token
        return $schema.' '.$token;
    }

    /**
     * @notes 发送请求
     * @param $url
     * @param $data
     * @param $token
     * @return bool|string
     * @author ljj
     * @date 2022/9/27 4:15 下午
     */
    public static function https_request($url,$data,$token)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, (string)$url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //添加请求头
        $headers = [
            'Authorization:'.$token,
            'Accept: application/json',
            'Content-Type: application/json; charset=utf-8',
            'User-Agent:Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36',
        ];
        if(!empty($headers)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /**
     * @notes 敏感信息加解密
     * @param $str
     * @param $config
     * @return string
     * @throws \Exception
     * @author ljj
     * @date 2022/9/27 3:53 下午
     */
    public static function getEncrypt($str,$config)
    {
        //$str是待加密字符串
        $public_key = file_get_contents($config['cert_path']);
        $encrypted = '';
        if (openssl_public_encrypt($str, $encrypted, $public_key, OPENSSL_PKCS1_OAEP_PADDING)) {
            //base64编码
            $sign = base64_encode($encrypted);
        } else {
            throw new \Exception('encrypt failed');
        }
        return $sign;
    }

    /**
     * @notes 商家明细单号查询明细单API
     * @param $withdrawApply
     * @return mixed
     * @author ljj
     * @date 2022/9/27 5:54 下午
     */
    public static function details($withdrawApply)
    {
        // 获取微信配置
        $config = WeChatConfigService::getWechatConfigByTerminal($withdrawApply['terminal']);
        //请求URL
        $url = 'https://api.mch.weixin.qq.com/v3/fund-app/mch-transfer/transfer-bills/out-bill-no/'.$withdrawApply['sn'];
        //请求方式
        $http_method = 'GET';
        //请求参数
        $data = [];
        $token  = self::token($url,$http_method,$data,$config);//获取token
        $result = self::https_request($url,$data,$token);//发送请求
        $result_arr = json_decode($result,true);
        return $result_arr;
    }

    /**
     * @notes 统一V3查询并更新状态（公共方法）
     * @param $withdrawApply
     * @return string 返回提示信息
     * @author optimize
     */
    public static function queryAndUpdateStatus($withdrawApply)
    {
        $tips = '正在处理中';
        $result = self::details($withdrawApply);

        if (!isset($result['state'])) {
            return $result['message'] ?? '商家转账到零钱查询失败';
        }

        $withdrawApply = WithdrawApply::findOrEmpty($withdrawApply['id']);
        if ($withdrawApply->isEmpty() || $withdrawApply->status != WithdrawEnum::STATUS_ING) {
            return '状态已变更，无需更新';
        }

        switch ($result['state']) {
            case 'SUCCESS':
                WithdrawApply::update([
                    'status' => WithdrawEnum::STATUS_SUCCESS,
                    'pay_search_result' => json_encode($result, JSON_UNESCAPED_UNICODE),
                    'payment_no' => $result['detail_id'] ?? '',
                    'payment_time' => strtotime($result['update_time'] ?? ''),
                ], ['id' => $withdrawApply['id']]);
                $tips = '提现成功';
                break;

            case 'FAIL':
                WithdrawApply::update([
                    'status' => WithdrawEnum::STATUS_FAIL,
                    'pay_search_result' => json_encode($result, JSON_UNESCAPED_UNICODE),
                ], ['id' => $withdrawApply['id']]);
                WithdrawLogic::fallbackMoney($withdrawApply);
                AccountLogLogic::add($withdrawApply['user_id'], AccountLogEnum::BW_INC_PAYMENT_FAIL, AccountLogEnum::INC, $withdrawApply['money'], $withdrawApply['sn'], '付款失败回退金额');
                $tips = '提现失败';
                break;

            case 'WAIT_USER_CONFIRM':
                $timeout = ConfigService::get('config', 'withdraw_confirm_timeout', 72);
                $elapsed = time() - strtotime($withdrawApply['create_time']);
                if ($elapsed > $timeout * 3600) {
                    WithdrawApply::update([
                        'status' => WithdrawEnum::STATUS_FAIL,
                        'pay_search_result' => json_encode($result, JSON_UNESCAPED_UNICODE),
                    ], ['id' => $withdrawApply['id']]);
                    WithdrawLogic::fallbackMoney($withdrawApply);
                    AccountLogLogic::add($withdrawApply['user_id'], AccountLogEnum::BW_INC_PAYMENT_FAIL, AccountLogEnum::INC, $withdrawApply['money'], $withdrawApply['sn'], '收款确认超时回退金额');
                    $tips = '收款确认超时，已自动关闭并回退金额';
                } else {
                    $existNotice = Notice::where([
                        ['user_id', '=', $withdrawApply['user_id']],
                        ['scene_id', '=', NoticeEnum::WITHDRAW_CONFIRM_NOTICE],
                        ['send_type', '=', NoticeEnum::SYSTEM],
                    ])->whereRaw("JSON_CONTAINS(extra, '{\"withdraw_id\":{$withdrawApply['id']}}')")->findOrEmpty();
                    if ($existNotice->isEmpty()) {
                        event('Notice', [
                            'scene_id' => NoticeEnum::WITHDRAW_CONFIRM_NOTICE,
                            'params' => [
                                'user_id' => $withdrawApply['user_id'],
                                'withdraw_id' => $withdrawApply['id'],
                            ]
                        ]);
                    }
                    $tips = '待收款用户确认';
                }
                break;

            case 'PROCESSING':
                $tips = '正在处理中';
                break;
        }

        return $tips;
    }
}