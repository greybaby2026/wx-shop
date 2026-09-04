<?php

namespace app\common\service\wecom;

use app\common\service\ai\AiChatService;
use app\common\model\KefuAiConfig;

class KefuCallbackService
{
    protected $corpId;
    protected $secret;
    protected $token;
    protected $encodingAesKey;
    protected $openKfid;

    public function __construct()
    {
        $this->corpId = KefuAiConfig::getConfigValue('wecom_corp_id', '');
        $this->secret = KefuAiConfig::getConfigValue('wecom_kefu_secret', '');
        $this->token = KefuAiConfig::getConfigValue('wecom_callback_token', '');
        $this->encodingAesKey = KefuAiConfig::getConfigValue('wecom_encoding_aes_key', '');
        $this->openKfid = KefuAiConfig::getConfigValue('wecom_open_kfid', '');
    }

    public function handleMessage()
    {
        $echostr = input('echostr', '');
        if (!empty($echostr)) {
            $this->handleUrlVerify($echostr);
            return;
        }

        $rawBody = file_get_contents('php://input');
        if (empty($rawBody)) {
            return;
        }

        $this->log('receive', '收到回调: ' . $rawBody);

        $xml = simplexml_load_string($rawBody, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($xml === false) {
            return;
        }

        $encrypt = (string)($xml->Encrypt ?? '');
        if (empty($encrypt)) {
            return;
        }

        $msgSignature = input('msg_signature', '');
        $timestamp = input('timestamp', '');
        $nonce = input('nonce', '');

        $decrypted = $this->decryptMsg($encrypt, $msgSignature, $timestamp, $nonce);
        if ($decrypted === false) {
            $this->log('error', '消息解密失败');
            return;
        }

        $msgXml = simplexml_load_string($decrypted, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($msgXml === false) {
            $this->log('error', '解密后XML解析失败: ' . $decrypted);
            return;
        }

        $msgType = (string)($msgXml->MsgType ?? '');
        $event = (string)($msgXml->Event ?? '');
        $fromUser = (string)($msgXml->FromUserName ?? '');
        $openKfid = (string)($msgXml->OpenKfId ?? '') ?: $this->openKfid;
        $callbackToken = (string)($msgXml->Token ?? '');

        $this->log('receive', "MsgType={$msgType}, Event={$event}, FromUser={$fromUser}, OpenKfId={$openKfid}, Token={$callbackToken}");

        if ($msgType === 'event' && $event === 'kf_msg_or_event') {
            $this->syncAndHandleMessages($openKfid, $callbackToken);
            return;
        }

        if ($msgType === 'event' && $event === 'enter_session') {
            $this->handleWelcome($fromUser, $openKfid);
            return;
        }

        if ($msgType === 'text') {
            $content = (string)($msgXml->Content ?? '');
            $this->log('receive', "直接文本消息: {$content}");
            $this->handleTextMessage($fromUser, $openKfid, $content);
            return;
        }
    }

    protected function syncAndHandleMessages($openKfid, $callbackToken = '')
    {
        $token = $this->getAccessToken();
        if (!$token) return;

        $cursor = cache('wecom_kefu_cursor') ?: '';

        $url = 'https://qyapi.weixin.qq.com/cgi-bin/kf/sync_msg?access_token=' . $token;
        $postData = [
            'cursor' => $cursor,
            'limit' => 1000,
            'voice_format' => 0,
            'open_kfid' => $openKfid,
        ];
        if (!empty($callbackToken)) {
            $postData['token'] = $callbackToken;
        }
        $data = json_encode($postData, JSON_UNESCAPED_UNICODE);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $resp = curl_exec($ch);
        curl_close($ch);

        $this->log('sync_msg', '响应: ' . $resp);

        $result = json_decode($resp, true);
        if (!isset($result['errcode']) || $result['errcode'] !== 0) {
            $this->log('error', 'sync_msg失败: ' . $resp);
            return;
        }

        if (!empty($result['next_cursor'])) {
            cache('wecom_kefu_cursor', $result['next_cursor'], 86400);
        }

        $msgList = $result['msg_list'] ?? [];
        if (empty($msgList)) {
            $this->log('sync_msg', '无新消息');
            return;
        }

        foreach ($msgList as $msg) {
            $this->processSyncedMessage($msg, $openKfid);
        }
    }

    protected function processSyncedMessage($msg, $openKfid)
    {
        $externalUserid = $msg['external_userid'] ?? '';
        $msgType = $msg['msgtype'] ?? '';
        $origin = $msg['origin'] ?? 0;
        $msgid = $msg['msgid'] ?? '';

        if ($origin === 5) {
            if ($msgType === 'text' && !empty($externalUserid)) {
                $processedKey = 'wecom_msg_processed_' . md5($msgid);
                if (!cache($processedKey)) {
                    cache($processedKey, 1, 300);
                    $content = $msg['text'] ?? [];
                    $text = $content['content'] ?? '';
                    $this->handleHumanReply($externalUserid, $openKfid, $text);
                }
            }
            return;
        }

        if ($origin === 4 && $msgType !== 'event') {
            return;
        }

        if ($msgType === 'event') {
            $event = $msg['event'] ?? [];
            $eventType = $event['event_type'] ?? '';
            $eventExternalUserid = $event['external_userid'] ?? '';
            $effectiveUserid = !empty($eventExternalUserid) ? $eventExternalUserid : $externalUserid;

            if (empty($effectiveUserid)) {
                return;
            }

            $processedKey = 'wecom_msg_processed_' . md5($msgid);
            if (cache($processedKey)) {
                return;
            }
            cache($processedKey, 1, 300);

            $this->log('sync_msg', "处理事件: msgid={$msgid}, external_userid={$effectiveUserid}, event_type={$eventType}, origin={$origin}");

            if ($eventType === 'enter_session') {
                $this->handleWelcome($effectiveUserid, $openKfid);
            }

            if ($eventType === 'session_status_change') {
                $changeType = $event['change_type'] ?? 0;
                $this->log('session_status', "会话状态变更: external_userid={$effectiveUserid}, change_type={$changeType}");

                if (in_array($changeType, [3, 4, 5])) {
                    $this->handleSessionEnd($effectiveUserid, $openKfid);
                }
            }
            return;
        }

        if (empty($externalUserid)) {
            return;
        }

        $processedKey = 'wecom_msg_processed_' . md5($msgid);
        if (cache($processedKey)) {
            return;
        }
        cache($processedKey, 1, 300);

        $this->log('sync_msg', "处理消息: msgid={$msgid}, external_userid={$externalUserid}, msgtype={$msgType}, origin={$origin}");

        if ($msgType === 'text') {
            $content = $msg['text'] ?? [];
            $text = $content['content'] ?? '';
            $this->log('sync_msg', "文本消息: {$text}");

            cache('kefu_last_question_' . $externalUserid, $text, 1800);

            if ($this->isHumanServing($externalUserid, $openKfid)) {
                $this->log('skip', "人工接待中，AI不回复: {$externalUserid}");
                return;
            }

            $this->handleTextMessage($externalUserid, $openKfid, $text);
            return;
        }

        if ($msgType === 'image') {
            if ($this->isHumanServing($externalUserid, $openKfid)) {
                return;
            }
            $this->handleTextMessage($externalUserid, $openKfid, '用户发送了一张图片');
            return;
        }

        if ($msgType === 'voice') {
            if ($this->isHumanServing($externalUserid, $openKfid)) {
                return;
            }
            $this->handleTextMessage($externalUserid, $openKfid, '用户发送了语音消息');
            return;
        }
    }

    protected function handleUrlVerify($echostr)
    {
        $msgSignature = input('msg_signature', '');
        $timestamp = input('timestamp', '');
        $nonce = input('nonce', '');

        $decrypted = $this->decryptMsg($echostr, $msgSignature, $timestamp, $nonce);
        if ($decrypted !== false) {
            echo $decrypted;
            exit;
        }

        echo 'verify failed';
        exit;
    }

    protected function handleWelcome($fromUser, $openKfid)
    {
        $this->log('welcome', "用户进入客服: {$fromUser}");

        $kefuMsg = new KefuMessageService();
        $kefuMsg->sendMenu($fromUser, $openKfid, '👋 您好！欢迎来到将军世家，我是AI智能客服，请选择您需要的服务：', [
            ['content' => '查询订单'],
            ['content' => '退换货'],
            ['content' => '物流查询'],
            ['content' => '转人工客服'],
        ], '如需其他帮助，请直接输入问题~');
    }

    protected function handleTextMessage($fromUser, $openKfid, $content)
    {
        try {
            $kefuMsg = new KefuMessageService();

            if (preg_match('/^20\d{16,18}$/', trim($content))) {
                $this->handleOrderBySn($fromUser, $openKfid, trim($content), $kefuMsg);
                return;
            }

            if (preg_match('/^1[3-9]\d{9}$/', trim($content))) {
                $this->handleOrderByPhone($fromUser, $openKfid, trim($content), $kefuMsg);
                return;
            }

            $menuAction = $this->resolveMenuAction($content);

            if ($menuAction === 'transfer_human') {
                cache('kefu_human_serving_' . $fromUser, '1', 3600);
                $kefuMsg->sendText($fromUser, $openKfid, "亲，已为您转接人工客服，请稍等片刻，客服专员会尽快为您处理~ 😊\n\n等待期间，您也可以描述一下遇到的问题，方便客服快速帮您解决哦~");
                $kefuMsg->transferToHuman($fromUser, $openKfid);
                $this->log('transfer', "用户请求转人工: {$fromUser}");
                return;
            }

            if ($menuAction) {
                $this->handleMenuAction($fromUser, $openKfid, $menuAction, $kefuMsg);
                return;
            }

            $aiChatService = new AiChatService();
            $aiResult = $aiChatService->handleMessage($content, $fromUser, $openKfid);

            $this->log('ai', "AI结果: " . json_encode($aiResult, JSON_UNESCAPED_UNICODE));

            if ($aiResult['success']) {
                $kefuMsg->sendText($fromUser, $openKfid, $aiResult['reply']);
                sleep(1);
                $kefuMsg->sendMenu($fromUser, $openKfid, '还有其他问题吗？', [
                    ['content' => '查询订单'],
                    ['content' => '退换货'],
                    ['content' => '物流查询'],
                    ['content' => '转人工客服'],
                ], '');
            } else {
                $kefuMsg->sendText($fromUser, $openKfid, "亲，非常抱歉，我暂时无法处理您的问题。请稍后再试，或点击「转人工客服」联系人工客服~");
            }
        } catch (\Exception $e) {
            $this->log('error', 'handleTextMessage异常: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        }
    }

    protected function resolveMenuAction($content)
    {
        $content = trim($content);

        $numberMap = [
            '1' => 'query_order',
            '2' => 'return_goods',
            '3' => 'check_logistics',
            '4' => 'transfer_human',
            '查询订单' => 'query_order',
            '退换货' => 'return_goods',
            '物流查询' => 'check_logistics',
            '转人工客服' => 'transfer_human',
        ];

        if (isset($numberMap[$content])) {
            return $numberMap[$content];
        }

        $menuKeywords = [
            'query_order' => ['查订单', '我的订单', '订单查询', '看订单'],
            'return_goods' => ['退货', '换货', '退款', '售后', '申请售后'],
            'check_logistics' => ['查物流', '物流信息', '快递查询', '看物流', '快递到哪了'],
        ];

        foreach ($menuKeywords as $action => $keywords) {
            foreach ($keywords as $kw) {
                if (mb_strpos($content, $kw) !== false) {
                    return $action;
                }
            }
        }
        return null;
    }

    protected function handleMenuAction($fromUser, $openKfid, $action, $kefuMsg)
    {
        switch ($action) {
            case 'query_order':
                $this->handleQueryOrder($fromUser, $openKfid, $kefuMsg);
                break;
            case 'return_goods':
                $this->handleReturnGoods($fromUser, $openKfid, $kefuMsg);
                break;
            case 'check_logistics':
                $this->handleCheckLogistics($fromUser, $openKfid, $kefuMsg);
                break;
        }
    }

    protected function handleQueryOrder($fromUser, $openKfid, $kefuMsg)
    {
        $userId = $this->resolveUserId($fromUser);

        if (!$userId) {
            cache('kefu_pending_intent_' . $fromUser, 'query_order', 300);
            $kefuMsg->sendText($fromUser, $openKfid, "亲，查询订单请选择以下方式：\n─────────────\n📱 方式一：输入您的手机号\n      （可查看所有订单）\n📦 方式二：输入订单号\n      （查询指定订单）\n─────────────\n💡 订单号可在小程序「我的」→「我的订单」中找到");
            return;
        }

        $this->sendOrderList($fromUser, $openKfid, $userId, $kefuMsg);
    }

    protected function handleOrderBySn($fromUser, $openKfid, $orderSn, $kefuMsg)
    {
        $order = \think\facade\Db::table('ls_order')
            ->where('sn', $orderSn)
            ->where('delete_time', null)
            ->field('id, sn, order_status, order_amount, create_time')
            ->find();

        if (empty($order)) {
            $kefuMsg->sendText($fromUser, $openKfid, "亲，未找到订单号 {$orderSn} 的记录哦~\n请确认订单号是否正确 🤔\n\n💡 订单号可在小程序「我的」→「我的订单」中找到");
            return;
        }

        $intent = cache('kefu_pending_intent_' . $fromUser);
        cache('kefu_pending_intent_' . $fromUser, null);

        if ($intent === 'check_logistics') {
            $this->sendLogisticsByOrder($fromUser, $openKfid, $order, $kefuMsg);
        } else {
            $this->sendOrderDetail($fromUser, $openKfid, $order, $kefuMsg);
        }
    }

    protected function sendOrderDetail($fromUser, $openKfid, $order, $kefuMsg)
    {
        $statusMap = [0 => '待支付', 1 => '待发货', 2 => '待收货', 3 => '已完成', 4 => '已取消'];
        $status = $statusMap[$order['order_status']] ?? '未知';
        $time = date('Y-m-d H:i', $order['create_time']);

        $msg = "📦 订单详情：\n─────────────\n";
        $msg .= "订单号：{$order['sn']}\n";
        $msg .= "状  态：{$status}\n";
        $msg .= "金  额：¥{$order['order_amount']}\n";
        $msg .= "时  间：{$time}\n";
        $msg .= "─────────────\n";

        if ($order['order_status'] == 2) {
            $delivery = \think\facade\Db::table('ls_delivery')
                ->where('order_id', $order['id'])
                ->field('invoice_no')
                ->find();
            if ($delivery && $delivery['invoice_no']) {
                $msg .= "🚚 快递单号：{$delivery['invoice_no']}\n";
            }
        }

        $msg .= "💡 返回小程序「我的」→「我的订单」可查看详情";
        $kefuMsg->sendText($fromUser, $openKfid, $msg);
    }

    protected function sendLogisticsByOrder($fromUser, $openKfid, $order, $kefuMsg)
    {
        if (!in_array($order['order_status'], [2, 3])) {
            $statusMap = [0 => '待支付', 1 => '待发货', 2 => '待收货', 3 => '已完成', 4 => '已取消'];
            $status = $statusMap[$order['order_status']] ?? '未知';
            $kefuMsg->sendText($fromUser, $openKfid, "亲，该订单当前状态为「{$status}」，暂无物流信息哦~\n\n📦 订单号：{$order['sn']}\n💰 金额：¥{$order['order_amount']}");
            return;
        }

        $delivery = \think\facade\Db::table('ls_delivery')
            ->where('order_id', $order['id'])
            ->field('invoice_no, order_sn')
            ->find();

        $statusMap = [2 => '运输中', 3 => '已签收'];
        $status = $statusMap[$order['order_status']] ?? '未知';

        $msg = "🚚 物流信息：\n─────────────\n";
        $msg .= "📦 订单号：{$order['sn']}\n";
        if ($delivery && $delivery['invoice_no']) {
            $msg .= "📋 快递单号：{$delivery['invoice_no']}\n";
        } else {
            $msg .= "📋 快递单号：暂无\n";
        }
        $msg .= "📍 状态：{$status}\n";
        $msg .= "─────────────\n";
        $msg .= "💡 返回小程序「我的」→「我的订单」可查看详细物流";
        $kefuMsg->sendText($fromUser, $openKfid, $msg);
    }

    protected function handleOrderByPhone($fromUser, $openKfid, $phone, $kefuMsg)
    {
        $user = \think\facade\Db::table('ls_user')
            ->where('mobile', $phone)
            ->where('delete_time', null)
            ->field('id, nickname')
            ->find();

        if (empty($user)) {
            $kefuMsg->sendText($fromUser, $openKfid, "亲，未找到与手机号 {$phone} 关联的账户哦~\n请确认手机号是否正确 🤔\n\n💡 也可直接输入订单号查询");
            return;
        }

        \think\facade\Db::table('ls_kefu_user_mapping')->insert([
            'external_userid' => $fromUser,
            'user_id' => $user['id'],
            'create_time' => time(),
        ]);
        cache('kefu_user_map_' . $fromUser, $user['id'], 3600);

        $this->log('mapping', "手机号映射成功: external_userid={$fromUser}, user_id={$user['id']}, phone={$phone}");

        $intent = cache('kefu_pending_intent_' . $fromUser);
        cache('kefu_pending_intent_' . $fromUser, null);

        if ($intent === 'check_logistics') {
            $this->sendLogisticsList($fromUser, $openKfid, $user['id'], $kefuMsg);
        } else {
            $this->sendOrderList($fromUser, $openKfid, $user['id'], $kefuMsg);
        }
    }

    protected function sendOrderList($fromUser, $openKfid, $userId, $kefuMsg)
    {
        $orders = \think\facade\Db::table('ls_order')
            ->where('user_id', $userId)
            ->where('delete_time', null)
            ->order('id', 'desc')
            ->limit(3)
            ->field('id, sn, order_status, order_amount, create_time')
            ->select()
            ->toArray();

        if (empty($orders)) {
            $kefuMsg->sendText($fromUser, $openKfid, "亲，您暂时没有订单记录哦~可以去逛逛挑选喜欢的商品 😊");
        } else {
            $statusMap = [0 => '待支付', 1 => '待发货', 2 => '待收货', 3 => '已完成', 4 => '已取消'];
            $msg = "📋 您最近的订单：\n";
            foreach ($orders as $order) {
                $status = $statusMap[$order['order_status']] ?? '未知';
                $time = date('Y-m-d H:i', $order['create_time']);
                $msg .= "─────────────\n";
                $msg .= "📦 订单号：{$order['sn']}\n";
                $msg .= "   状态：{$status} | 金额：¥{$order['order_amount']}\n";
                $msg .= "   时间：{$time}\n";
            }
            $msg .= "─────────────\n";
            $msg .= "💡 返回小程序「我的」→「我的订单」可查看详情";
            $kefuMsg->sendText($fromUser, $openKfid, $msg);
        }
    }

    protected function handleReturnGoods($fromUser, $openKfid, $kefuMsg)
    {
        $msg = "🔄 退换货流程：\n─────────────\n";
        $msg .= "1️⃣ 在小程序「我的」→「我的订单」\n";
        $msg .= "2️⃣ 找到需要退换的订单\n";
        $msg .= "3️⃣ 点击「申请售后」\n";
        $msg .= "4️⃣ 填写退换原因并提交\n";
        $msg .= "5️⃣ 等待客服审核通过后寄回商品\n\n";
        $msg .= "💡 温馨提示：商品需保持原包装，不影响二次销售哦~";
        $kefuMsg->sendText($fromUser, $openKfid, $msg);
    }

    protected function handleCheckLogistics($fromUser, $openKfid, $kefuMsg)
    {
        $userId = $this->resolveUserId($fromUser);

        if (!$userId) {
            cache('kefu_pending_intent_' . $fromUser, 'check_logistics', 300);
            $kefuMsg->sendText($fromUser, $openKfid, "亲，物流查询请选择以下方式：\n─────────────\n📱 方式一：输入您的手机号\n      （可查看所有物流）\n📦 方式二：输入订单号\n      （查询指定订单物流）\n─────────────\n💡 订单号可在小程序「我的」→「我的订单」中找到");
            return;
        }

        $this->sendLogisticsList($fromUser, $openKfid, $userId, $kefuMsg);
    }

    protected function sendLogisticsList($fromUser, $openKfid, $userId, $kefuMsg)
    {
        $delivery = \think\facade\Db::table('ls_delivery')
            ->alias('d')
            ->join('ls_order o', 'd.order_id = o.id')
            ->where('o.user_id', $userId)
            ->where('o.order_status', 'in', [2, 3])
            ->where('o.delete_time', null)
            ->order('d.id', 'desc')
            ->limit(3)
            ->field('d.order_id, d.invoice_no, d.order_sn, o.order_status')
            ->select()
            ->toArray();

        if (empty($delivery)) {
            $kefuMsg->sendText($fromUser, $openKfid, "亲，您暂时没有在途的物流信息哦~\n\n💡 返回小程序「我的」→「我的订单」可查看所有订单状态");
        } else {
            $statusMap = [2 => '运输中', 3 => '已签收'];
            $msg = "🚚 您的物流信息：\n";
            foreach ($delivery as $item) {
                $status = $statusMap[$item['order_status']] ?? '未知';
                $msg .= "─────────────\n";
                $msg .= "📦 订单号：{$item['order_sn']}\n";
                $msg .= "   快递单号：{$item['invoice_no']}\n";
                $msg .= "   状态：{$status}\n";
            }
            $msg .= "─────────────\n";
            $msg .= "💡 返回小程序「我的」→「我的订单」可查看详细物流";
            $kefuMsg->sendText($fromUser, $openKfid, $msg);
        }
    }

    protected function resolveUserId($externalUserid)
    {
        $cached = cache('kefu_user_map_' . $externalUserid);
        if ($cached) {
            return $cached;
        }

        $mapping = \think\facade\Db::table('ls_kefu_user_mapping')
            ->where('external_userid', $externalUserid)
            ->find();

        if ($mapping) {
            cache('kefu_user_map_' . $externalUserid, $mapping['user_id'], 3600);
            return $mapping['user_id'];
        }

        $token = $this->getAccessToken();
        if (!$token) return 0;

        $url = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/get?access_token=' . $token . '&external_userid=' . $externalUserid;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($resp, true);
        if (isset($result['external_contact']['unionid'])) {
            $unionid = $result['external_contact']['unionid'];

            $userAuth = \think\facade\Db::table('ls_user_auth')
                ->where('unionid', $unionid)
                ->find();

            if ($userAuth) {
                $userId = $userAuth['user_id'];

                \think\facade\Db::table('ls_kefu_user_mapping')->insert([
                    'external_userid' => $externalUserid,
                    'user_id' => $userId,
                    'create_time' => time(),
                ]);

                cache('kefu_user_map_' . $externalUserid, $userId, 3600);
                return $userId;
            }
        }

        return 0;
    }

    protected function handleHumanReply($externalUserid, $openKfid, $replyText)
    {
        $this->log('human_reply', "人工客服回复: external_userid={$externalUserid}, text=" . mb_substr($replyText, 0, 100));

        if (mb_strlen($replyText) < 3) {
            return;
        }

        $skipPatterns = ['亲，已为您转接', '人工客服已结束', '请稍等', '感谢您的', '欢迎光临', '您好', '有什么可以帮', '好的', '收到', '嗯嗯', '稍等'];
        foreach ($skipPatterns as $pattern) {
            if (mb_strpos($replyText, $pattern) !== false) {
                return;
            }
        }

        $lastUserQuestion = cache('kefu_last_question_' . $externalUserid);

        if (empty($lastUserQuestion) || $lastUserQuestion === 'greeting' || mb_strlen($lastUserQuestion) < 2) {
            return;
        }

        $similarFaq = \think\facade\Db::table('ls_kefu_faq')
            ->where('status', 1)
            ->where(function ($q) use ($lastUserQuestion) {
                $q->whereOr('question', $lastUserQuestion)
                  ->whereOr('question', 'like', '%' . mb_substr($lastUserQuestion, 0, 4) . '%');
            })
            ->field('id, question, answer, hit_count')
            ->find();

        if ($similarFaq) {
            \think\facade\Db::table('ls_kefu_faq')
                ->where('id', $similarFaq['id'])
                ->update([
                    'answer' => $replyText,
                    'hit_count' => $similarFaq['hit_count'] + 1,
                    'update_time' => time(),
                    'expire_time' => time() + 30 * 86400,
                ]);
            $this->log('auto_learn', "FAQ更新: question={$similarFaq['question']}, new_answer=" . mb_substr($replyText, 0, 50));
        } else {
            \think\facade\Db::table('ls_kefu_faq')->insert([
                'question' => $lastUserQuestion,
                'answer' => $replyText,
                'category' => $this->classifyFromQuestion($lastUserQuestion),
                'hit_count' => 1,
                'status' => 1,
                'expire_time' => time() + 30 * 86400,
                'create_time' => time(),
                'update_time' => time(),
            ]);
            $this->log('auto_learn', "FAQ新增: question={$lastUserQuestion}, answer=" . mb_substr($replyText, 0, 50));
        }

        cache('kefu_last_question_' . $externalUserid, null);

        \think\facade\Db::table('ls_kefu_faq')
            ->where('expire_time', '>', 0)
            ->where('expire_time', '<', time())
            ->delete();

        cache('kefu_faq_active_list', null);
    }

    protected function classifyFromQuestion($question)
    {
        $categoryKeywords = [
            'product' => ['商品', '产品', '价格', '多少钱', '面料', '材质', '尺码', '尺寸', '颜色'],
            'order' => ['订单', '下单', '购买', '支付', '付款', '购物车', '优惠券'],
            'after_sale' => ['退货', '退款', '换货', '售后', '维修', '投诉'],
            'distribution' => ['分销', '佣金', '提成', '推广', '团长'],
            'delivery' => ['发货', '物流', '快递', '配送', '收货'],
        ];

        foreach ($categoryKeywords as $cat => $words) {
            foreach ($words as $word) {
                if (mb_strpos($question, $word) !== false) {
                    return $cat;
                }
            }
        }

        return 'general';
    }

    protected function handleSessionEnd($externalUserid, $openKfid)
    {
        $this->log('session_end', "人工会话结束，切回AI: {$externalUserid}");

        cache('kefu_human_serving_' . $externalUserid, null);

        $kefuMsg = new KefuMessageService();
        $kefuMsg->transferToHuman($externalUserid, $openKfid, 1);

        sleep(1);

        $kefuMsg->sendMenu($externalUserid, $openKfid, '😊 人工客服已结束服务，我将继续为您服务~有什么可以帮您的吗？', [
            ['content' => '查询订单'],
            ['content' => '退换货'],
            ['content' => '物流查询'],
            ['content' => '转人工客服'],
        ], '');
    }

    protected function isHumanServing($externalUserid, $openKfid)
    {
        $cached = cache('kefu_human_serving_' . $externalUserid);
        if ($cached === '1') {
            $lastCheck = cache('kefu_human_check_' . $externalUserid);
            if ($lastCheck && time() - $lastCheck < 30) {
                return true;
            }
        }

        $token = $this->getAccessToken();
        if (!$token) return false;

        $url = 'https://qyapi.weixin.qq.com/cgi-bin/kf/service_state/get?access_token=' . $token;
        $postData = json_encode([
            'open_kfid' => $openKfid,
            'external_userid' => $externalUserid,
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $resp = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($resp, true);
        $serviceState = $result['service_state'] ?? 0;

        $this->log('service_state', "external_userid={$externalUserid}, state={$serviceState}");

        if ($serviceState == 3) {
            cache('kefu_human_serving_' . $externalUserid, '1', 3600);
            cache('kefu_human_check_' . $externalUserid, time(), 60);
            return true;
        }

        if ($serviceState == 4 || $serviceState == 0) {
            $this->log('service_state', "会话已结束或未处理，切回AI: state={$serviceState}");
            $kefuMsg = new KefuMessageService();
            $kefuMsg->transferToHuman($externalUserid, $openKfid, 1);
            cache('kefu_human_serving_' . $externalUserid, null);
            cache('kefu_human_check_' . $externalUserid, null);
            return false;
        }

        cache('kefu_human_serving_' . $externalUserid, null);
        cache('kefu_human_check_' . $externalUserid, null);
        return false;
    }

    protected function getAccessToken()
    {
        $cache = cache('wecom_kefu_access_token');
        if ($cache) {
            return $cache;
        }

        $url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=' . $this->corpId . '&corpsecret=' . $this->secret;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($resp, true);
        if (isset($data['access_token'])) {
            cache('wecom_kefu_access_token', $data['access_token'], $data['expires_in'] - 300);
            return $data['access_token'];
        }

        $this->log('error', '获取access_token失败: ' . $resp);
        return null;
    }

    protected function decryptMsg($encrypt, $msgSignature, $timestamp, $nonce)
    {
        $sortArr = [$this->token, $timestamp, $nonce, $encrypt];
        sort($sortArr, SORT_STRING);
        $checkSignature = sha1(implode('', $sortArr));

        if ($checkSignature !== $msgSignature) {
            $this->log('error', "签名验证失败: calc={$checkSignature}, recv={$msgSignature}");
            return false;
        }

        $aesKey = base64_decode($this->encodingAesKey . '=');
        $ciphertext = base64_decode($encrypt);

        $decrypted = openssl_decrypt(
            $ciphertext,
            'AES-256-CBC',
            $aesKey,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            substr($aesKey, 0, 16)
        );

        if ($decrypted === false) {
            $this->log('error', 'AES解密失败');
            return false;
        }

        $pad = ord(substr($decrypted, -1));
        $decrypted = substr($decrypted, 0, -$pad);

        $contentLen = unpack('N', substr($decrypted, 16, 4))[1];
        $content = substr($decrypted, 20, $contentLen);
        $fromCorpid = substr($decrypted, 20 + $contentLen);

        if ($fromCorpid !== $this->corpId) {
            $this->log('error', "CorpID不匹配: {$fromCorpid} != {$this->corpId}");
            return false;
        }

        return $content;
    }

    protected function log($prefix, $msg)
    {
        $logDir = '/www/wwwroot/jiangjunshijia_com/server/runtime/shopapi/wecom_kefu';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
            @chown($logDir, 'www');
            @chgrp($logDir, 'www');
        }
        $logFile = $logDir . '/' . date('Y-m-d') . '.log';
        $time = date('Y-m-d H:i:s');
        @file_put_contents($logFile, "[{$time}] [{$prefix}] {$msg}\n", FILE_APPEND | LOCK_EX);
        \think\facade\Log::write("WECOM_KEFU_{$prefix}: {$msg}", 'info');
    }
}
