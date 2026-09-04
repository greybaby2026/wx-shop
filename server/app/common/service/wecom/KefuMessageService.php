<?php

namespace app\common\service\wecom;

use app\common\model\KefuAiConfig;

class KefuMessageService
{
    protected $corpId;
    protected $secret;
    protected $openKfid;

    public function __construct()
    {
        $this->corpId = KefuAiConfig::getConfigValue('wecom_corp_id', '');
        $this->secret = KefuAiConfig::getConfigValue('wecom_kefu_secret', '');
        $this->openKfid = KefuAiConfig::getConfigValue('wecom_open_kfid', '');
    }

    public function sendText($externalUserid, $openKfid, $content)
    {
        $token = $this->getAccessToken();
        if (!$token) return false;

        $url = 'https://qyapi.weixin.qq.com/cgi-bin/kf/send_msg?access_token=' . $token;
        $data = json_encode([
            'touser' => $externalUserid,
            'open_kfid' => $openKfid,
            'msgtype' => 'text',
            'text' => ['content' => $content],
        ], JSON_UNESCAPED_UNICODE);

        $resp = $this->httpPost($url, $data);
        $this->log('send_text', "touser={$externalUserid}, response=" . $resp);

        $result = json_decode($resp, true);
        if (isset($result['errcode']) && $result['errcode'] === 42001) {
            cache('wecom_kefu_access_token', null);
            return $this->sendText($externalUserid, $openKfid, $content);
        }

        return true;
    }

    public function sendMenu($externalUserid, $openKfid, $headContent, $list, $tailContent = '')
    {
        $token = $this->getAccessToken();
        if (!$token) return false;

        $url = 'https://qyapi.weixin.qq.com/cgi-bin/kf/send_msg?access_token=' . $token;

        $items = [];
        foreach ($list as $item) {
            $items[] = ['type' => 'click', 'click' => ['id' => md5($item['content']), 'content' => $item['content']]];
        }

        $data = json_encode([
            'touser' => $externalUserid,
            'open_kfid' => $openKfid,
            'msgtype' => 'msgmenu',
            'msgmenu' => [
                'head_content' => $headContent,
                'list' => $items,
                'tail_content' => $tailContent,
            ],
        ], JSON_UNESCAPED_UNICODE);

        $resp = $this->httpPost($url, $data);
        $this->log('send_menu', "touser={$externalUserid}, response=" . $resp);

        $result = json_decode($resp, true);
        if (isset($result['errcode']) && $result['errcode'] === 42001) {
            cache('wecom_kefu_access_token', null);
            return $this->sendMenu($externalUserid, $openKfid, $headContent, $list, $tailContent);
        }

        return true;
    }

    public function sendLink($externalUserid, $openKfid, $title, $desc, $url, $thumbMediaId = '')
    {
        $token = $this->getAccessToken();
        if (!$token) return false;

        $apiUrl = 'https://qyapi.weixin.qq.com/cgi-bin/kf/send_msg?access_token=' . $token;

        $linkData = [
            'title' => $title,
            'desc' => $desc,
            'url' => $url,
        ];
        if ($thumbMediaId) {
            $linkData['thumb_media_id'] = $thumbMediaId;
        }

        $data = json_encode([
            'touser' => $externalUserid,
            'open_kfid' => $openKfid,
            'msgtype' => 'link',
            'link' => $linkData,
        ], JSON_UNESCAPED_UNICODE);

        $resp = $this->httpPost($apiUrl, $data);
        $this->log('send_link', "touser={$externalUserid}, title={$title}, response=" . $resp);

        return true;
    }

    public function getCustomerUnionId($externalUserid)
    {
        $cached = cache('wecom_unionid_' . $externalUserid);
        if ($cached) {
            return $cached;
        }

        $token = $this->getAccessToken();
        if (!$token) return null;

        $url = 'https://qyapi.weixin.qq.com/cgi-bin/externalcontact/get?access_token=' . $token . '&external_userid=' . $externalUserid;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($resp, true);
        if (isset($result['external_contact']['unionid'])) {
            $unionid = $result['external_contact']['unionid'];
            cache('wecom_unionid_' . $externalUserid, $unionid, 86400);
            return $unionid;
        }

        $this->log('error', '获取unionid失败: ' . $resp);
        return null;
    }

    public function transferToHuman($externalUserid, $openKfid, $serviceState = 3)
    {
        $token = $this->getAccessToken();
        if (!$token) return false;

        $url = 'https://qyapi.weixin.qq.com/cgi-bin/kf/service_state/trans?access_token=' . $token;

        $servicerUserid = KefuAiConfig::getConfigValue('wecom_servicer_userid', '');

        $data = json_encode([
            'open_kfid' => $openKfid,
            'external_userid' => $externalUserid,
            'service_state' => $serviceState,
            'servicer_userid' => $servicerUserid,
        ], JSON_UNESCAPED_UNICODE);

        $resp = $this->httpPost($url, $data);
        $this->log('transfer', "external_userid={$externalUserid}, service_state={$serviceState}, response=" . $resp);

        return true;
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

    protected function httpPost($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $resp = curl_exec($ch);
        curl_close($ch);
        return $resp;
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
    }
}
