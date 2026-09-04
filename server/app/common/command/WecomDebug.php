<?php

namespace app\common\command;

use app\common\model\KefuAiConfig;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class WecomDebug extends Command
{
    protected function configure()
    {
        $this->setName('wecom-debug')->setDescription('调试企微回调验证');
    }

    protected function execute(Input $input, Output $output)
    {
        $token = KefuAiConfig::getConfigValue('wecom_callback_token');
        $aesKey = KefuAiConfig::getConfigValue('wecom_encoding_aes_key');
        $corpId = KefuAiConfig::getConfigValue('wecom_corp_id');

        $output->writeln("Token: {$token}");
        $output->writeln("AESKey: {$aesKey}");
        $output->writeln("CorpId: {$corpId}");

        $aesKeyBin = base64_decode($aesKey . '=');
        $output->writeln("AESKey二进制长度: " . strlen($aesKeyBin));

        $echostr = "weprFYnqHVQobDcDk0BDLBGT7TCRxNtO4SlLSclXhyaBdH3R6YLL8wbojoj2IgEc4XrvBSdPTVmssMs0uRKvwg==";
        $msgSignature = "4716bc13c52775d52a3dc768861d3e57eeabebda";
        $timestamp = "1778480270";
        $nonce = "1779243860";

        $array = [$echostr, $token, $timestamp, $nonce];
        sort($array, SORT_STRING);
        $signature = sha1(implode('', $array));

        $output->writeln("企微签名: {$msgSignature}");
        $output->writeln("计算签名: {$signature}");
        $output->writeln("签名匹配: " . ($signature === $msgSignature ? 'YES' : 'NO'));

        if ($signature === $msgSignature) {
            $ciphertext = base64_decode($echostr);
            $output->writeln("解密前长度: " . strlen($ciphertext));

            $iv = substr($ciphertext, 0, 16);
            $encrypted = substr($ciphertext, 16);

            $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $aesKeyBin, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);

            if ($decrypted === false) {
                $output->writeln("解密失败: " . openssl_error_string());
            } else {
                $pad = ord($decrypted[strlen($decrypted) - 1]);
                $result = substr($decrypted, 0, strlen($decrypted) - $pad);
                $content = substr($result, 16);
                $length = unpack('N', substr($content, 0, 4))[1];
                $msg = substr($content, 4, $length);
                $appid = substr($content, 4 + $length);

                $output->writeln("明文长度: {$length}");
                $output->writeln("消息: {$msg}");
                $output->writeln("AppID: {$appid}");
                $output->writeln("解密成功: YES");
            }
        }
    }
}