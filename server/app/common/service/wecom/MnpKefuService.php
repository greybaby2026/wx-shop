<?php

namespace app\common\service\wecom;

use app\common\service\ai\AiChatService;

class MnpKefuService
{
    protected $appId;
    protected $appSecret;
    protected $token;
    protected $encodingAesKey;

    public function __construct()
    {
        $this->appId = \app\common\service\ConfigService::get('mini_program', 'app_id', '');
        $this->appSecret = \app\common\service\ConfigService::get('mini_program', 'app_secret', '');
        $this->token = 'jiangjunshijia_kefu';
        $this->encodingAesKey = '2DB7g5pFzTZHs3CdE5DlcdkRFRj2zxgUJ6CYKredvMY';
    }

    public function handleMessage()
    {
        $echostr = input('echostr', '');
        if (!empty($echostr)) {
            echo $echostr;
            exit;
        }

        $rawBody = file_get_contents('php://input');
        if (empty($rawBody)) {
            return;
        }

        $this->log('receive', '收到消息: ' . $rawBody);

        $data = json_decode($rawBody, true);
        if (empty($data)) {
            $xml = simplexml_load_string($rawBody, 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($xml === false) {
                return;
            }
            $data = json_decode(json_encode($xml), true);
        }

        $msgType = $data['MsgType'] ?? $data['msgtype'] ?? '';
        $fromUser = $data['FromUserName'] ?? $data['from_username'] ?? '';
        $toUser = $data['ToUserName'] ?? $data['to_username'] ?? '';

        if ($msgType === 'event') {
            $event = $data['Event'] ?? $data['event'] ?? '';
            if ($event === 'user_enter_tempsession') {
                $this->log('welcome', "用户进入客服: {$fromUser}");
                $this->sendWelcome($fromUser);
            }
            return;
        }

        if ($msgType === 'text') {
            $content = $data['Content'] ?? $data['content'] ?? '';
            $this->log('text', "用户消息: from={$fromUser}, content={$content}");
            $this->handleTextMessage($fromUser, $content);
            return;
        }

        if ($msgType === 'image') {
            $this->sendText($fromUser, '亲，暂时无法识别图片消息，请用文字描述您的问题哦~ 😊');
            return;
        }
    }

    protected function handleTextMessage($fromUser, $content)
    {
        $content = trim($content);

        if ($content === '结束人工' || $content === '退出人工') {
            cache('mnp_human_serving_' . $fromUser, null);
            $this->sendText($fromUser, "亲，已退出人工客服模式，AI客服将继续为您服务~ 😊");
            $this->log('transfer', "用户退出人工: {$fromUser}");
            return;
        }

        if (cache('mnp_human_serving_' . $fromUser)) {
            $this->log('skip', "人工接待中，AI不回复: {$fromUser}, msg={$content}");
            $this->notifyHumanAgent($fromUser, $content);
            return;
        }

        if (preg_match('/^20\d{16,18}$/', $content)) {
            $this->handleOrderBySn($fromUser, $content);
            return;
        }

        if (preg_match('/^1[3-9]\d{9}$/', $content)) {
            $this->handleOrderByPhone($fromUser, $content);
            return;
        }

        $menuAction = $this->resolveMenuAction($content);

        if ($menuAction === 'transfer_human') {
            cache('mnp_human_serving_' . $fromUser, '1', 3600);
            $this->sendText($fromUser, "亲，已为您转接人工客服，请稍等片刻~ 😊\n\n等待期间，您也可以描述一下遇到的问题哦~");
            $this->notifyHumanAgent($fromUser, '用户请求转人工客服');
            $this->log('transfer', "用户请求转人工: {$fromUser}");
            return;
        }

        if ($menuAction) {
            $this->handleMenuAction($fromUser, $menuAction);
            return;
        }

        try {
            $aiChatService = new AiChatService();
            $result = $aiChatService->handleMessage($content, $fromUser, '');

            if ($result['success']) {
                $reply = $result['reply'];
                if (!empty($result['knowledge_ids'])) {
                    $reply .= "\n\n💡 如需人工帮助，请回复「转人工」";
                }
                $this->sendText($fromUser, $reply);
                $this->log('ai', "AI回复: {$reply}");
            } else {
                $this->sendText($fromUser, '亲，我暂时无法回答这个问题，建议您回复「转人工」联系客服专员哦~ 😊');
            }
        } catch (\Exception $e) {
            $this->log('error', 'AI处理异常: ' . $e->getMessage());
            $this->sendText($fromUser, '亲，系统开小差了，请稍后再试或回复「转人工」~ 😊');
        }
    }

    protected function resolveMenuAction($content)
    {
        $map = [
            '查询订单' => 'query_order',
            '订单查询' => 'query_order',
            '查订单' => 'query_order',
            '退换货' => 'return_goods',
            '退货' => 'return_goods',
            '退款' => 'return_goods',
            '换货' => 'return_goods',
            '物流查询' => 'query_logistics',
            '查物流' => 'query_logistics',
            '快递' => 'query_logistics',
            '转人工' => 'transfer_human',
            '人工客服' => 'transfer_human',
            '转人工客服' => 'transfer_human',
            '人工' => 'transfer_human',
        ];
        return $map[$content] ?? '';
    }

    protected function handleMenuAction($fromUser, $action)
    {
        switch ($action) {
            case 'query_order':
                $this->sendText($fromUser, "亲，请输入您的订单号（20位数字）或手机号，我来帮您查询~ 📋");
                break;
            case 'return_goods':
                $this->sendText($fromUser, "亲，将军世家支持7天无理由退换货~\n\n请回复「转人工」联系客服专员处理退换货事宜，我们会尽快为您解决！😊");
                break;
            case 'query_logistics':
                $this->sendText($fromUser, "亲，请输入您的订单号，我来帮您查询物流信息~ 🚚");
                break;
        }
    }

    protected function handleOrderBySn($fromUser, $orderSn)
    {
        try {
            $order = \think\facade\Db::name('order')
                ->where('order_sn', $orderSn)
                ->where('delete_time', null)
                ->find();

            if (empty($order)) {
                $this->sendText($fromUser, "亲，未找到订单号 {$orderSn} 的记录，请确认订单号是否正确~ 🤔");
                return;
            }

            $statusMap = [0 => '待付款', 1 => '待发货', 2 => '待收货', 3 => '已完成', 4 => '已取消'];
            $status = $statusMap[$order['order_status']] ?? '未知';
            $reply = "📦 订单信息\n";
            $reply .= "订单号：{$orderSn}\n";
            $reply .= "状态：{$status}\n";
            $reply .= "金额：¥{$order['order_amount']}\n";
            $reply .= "下单时间：{$order['create_time']}\n\n";
            $reply .= "如需其他帮助，请回复「转人工」~ 😊";

            $this->sendText($fromUser, $reply);
        } catch (\Exception $e) {
            $this->log('error', '查询订单异常: ' . $e->getMessage());
            $this->sendText($fromUser, '亲，查询出错，请稍后再试或回复「转人工」~ 😊');
        }
    }

    protected function handleOrderByPhone($fromUser, $phone)
    {
        try {
            $user = \think\facade\Db::name('user')
                ->where('mobile', $phone)
                ->where('delete_time', null)
                ->find();

            if (empty($user)) {
                $this->sendText($fromUser, "亲，未找到手机号 {$phone} 关联的订单，请确认手机号是否正确~ 🤔");
                return;
            }

            $orders = \think\facade\Db::name('order')
                ->where('user_id', $user['id'])
                ->where('delete_time', null)
                ->order('id', 'desc')
                ->limit(3)
                ->select()
                ->toArray();

            if (empty($orders)) {
                $this->sendText($fromUser, "亲，该手机号暂无订单记录~ 🤔");
                return;
            }

            $statusMap = [0 => '待付款', 1 => '待发货', 2 => '待收货', 3 => '已完成', 4 => '已取消'];
            $reply = "📦 最近订单\n";
            foreach ($orders as $i => $order) {
                $status = $statusMap[$order['order_status']] ?? '未知';
                $reply .= ($i + 1) . ". {$order['order_sn']} | {$status} | ¥{$order['order_amount']}\n";
            }
            $reply .= "\n如需详细查询，请回复「转人工」~ 😊";

            $this->sendText($fromUser, $reply);
        } catch (\Exception $e) {
            $this->log('error', '查询订单异常: ' . $e->getMessage());
            $this->sendText($fromUser, '亲，查询出错，请稍后再试或回复「转人工」~ 😊');
        }
    }

    protected function sendWelcome($fromUser)
    {
        $msg = "👋 您好！欢迎来到将军世家，我是AI智能客服\n\n";
        $msg .= "请选择您需要的服务：\n";
        $msg .= "1️⃣ 查询订单\n";
        $msg .= "2️⃣ 退换货\n";
        $msg .= "3️⃣ 物流查询\n";
        $msg .= "4️⃣ 转人工客服\n\n";
        $msg .= "如需其他帮助，请直接输入问题~ 😊";

        $this->sendText($fromUser, $msg);
    }

    protected function sendText($touser, $content)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            $this->log('error', '获取access_token失败');
            return;
        }

        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$token}";
        $postData = json_encode([
            'touser' => $touser,
            'msgtype' => 'text',
            'text' => ['content' => $content]
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $resp = curl_exec($ch);
        curl_close($ch);

        $this->log('send_text', "touser={$touser}, response={$resp}");
    }

    protected function getAccessToken()
    {
        $cacheKey = 'mnp_kefu_access_token';
        $token = cache($cacheKey);
        if ($token) {
            return $token;
        }

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appId}&secret={$this->appSecret}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $resp = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($resp, true);
        if (isset($result['access_token'])) {
            cache($cacheKey, $result['access_token'], 7000);
            return $result['access_token'];
        }

        $this->log('error', '获取access_token失败: ' . $resp);
        return false;
    }

    protected function notifyHumanAgent($fromUser, $message)
    {
        $webhook = \app\common\model\KefuAiConfig::getConfigValue('wecom_report_webhook', '');
        if (empty($webhook)) {
            return;
        }

        $content = "🔔 **小程序客服转人工通知**\n\n";
        $content .= "> 用户OpenID：`{$fromUser}`\n";
        $content .= "> 消息内容：{$message}\n";
        $content .= "> 时间：" . date('Y-m-d H:i:s') . "\n\n";
        $content .= "请前往 [小程序后台](https://mp.weixin.qq.com/) → 客服 进行回复";

        $postData = json_encode([
            'msgtype' => 'markdown',
            'markdown' => ['content' => $content]
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init($webhook);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        curl_close($ch);
    }

    protected function log($prefix, $msg)
    {
        $logDir = '/www/wwwroot/jiangjunshijia_com/server/runtime/shopapi/mnp_kefu';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . '/' . date('Y-m-d') . '.log';
        $time = date('Y-m-d H:i:s');
        @file_put_contents($logFile, "[{$time}] [{$prefix}] {$msg}\n", FILE_APPEND | LOCK_EX);
    }
}
