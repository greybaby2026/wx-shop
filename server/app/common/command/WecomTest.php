<?php

namespace app\common\command;

use app\common\model\KefuAiConfig;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class WecomTest extends Command
{
    protected function configure()
    {
        $this->setName('wecom-test')->setDescription('测试企业微信API连通性');
    }

    protected function execute(Input $input, Output $output)
    {
        $corpId = KefuAiConfig::getConfigValue('wecom_corp_id');
        $secret = KefuAiConfig::getConfigValue('wecom_kefu_secret');

        $output->writeln("corpId: {$corpId}");
        $output->writeln("Secret: " . substr($secret, 0, 8) . "...");

        $client = new \GuzzleHttp\Client(['timeout' => 10]);

        try {
            $response = $client->get('https://qyapi.weixin.qq.com/cgi-bin/gettoken', [
                'query' => [
                    'corpid' => $corpId,
                    'corpsecret' => $secret,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['access_token'])) {
                $output->writeln("✅ access_token获取成功！");
                $output->writeln("token: " . substr($result['access_token'], 0, 20) . "...");
                $output->writeln("expires_in: " . ($result['expires_in'] ?? 'N/A') . "秒");

                $client = new \GuzzleHttp\Client(['timeout' => 10]);
                $kfList = $client->get('https://qyapi.weixin.qq.com/cgi-bin/kf/account/list', [
                    'query' => ['access_token' => $result['access_token']],
                    'json' => [],
                ]);

                $kfResult = json_decode($kfList->getBody()->getContents(), true);
                if (isset($kfResult['errcode']) && $kfResult['errcode'] === 0) {
                    $output->writeln("✅ 客服列表获取成功！");
                    foreach ($kfResult['account_list'] ?? [] as $acc) {
                        $output->writeln("  - open_kfid: {$acc['open_kfid']}");
                        $output->writeln("    名称: {$acc['name']}");
                        $output->writeln("    接待方式: {$acc['servicer_type']}");
                    }
                } else {
                    $output->writeln("❌ 客服列表获取失败: " . ($kfResult['errmsg'] ?? '未知'));
                }
            } else {
                $output->writeln("❌ 获取access_token失败: " . ($result['errmsg'] ?? '未知'));
            }
        } catch (\Exception $e) {
            $output->writeln("❌ 请求异常: " . $e->getMessage());
        }
    }
}