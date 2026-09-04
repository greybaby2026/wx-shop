<?php

namespace app\common\service\ai;

use app\common\model\KefuAiConfig;
use GuzzleHttp\Client;

class DeepSeekService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl = 'https://api.deepseek.com';
    protected $model;

    public function __construct()
    {
        $this->apiKey = KefuAiConfig::getConfigValue('deepseek_api_key', '');
        $this->model = KefuAiConfig::getConfigValue('deepseek_model', 'deepseek-chat');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function chat($messages, $options = [])
    {
        $temperature = $options['temperature'] ?? (float)KefuAiConfig::getConfigValue('deepseek_temperature', '0.7');
        $maxTokens = $options['max_tokens'] ?? (int)KefuAiConfig::getConfigValue('deepseek_max_tokens', '1024');

        $body = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
        ];

        try {
            $response = $this->client->post('/v1/chat/completions', [
                'json' => $body,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['choices'][0]['message']['content'])) {
                return [
                    'success' => true,
                    'content' => $result['choices'][0]['message']['content'],
                    'usage' => $result['usage'] ?? [],
                    'model' => $result['model'] ?? $this->model,
                ];
            }

            return ['success' => false, 'error' => 'DeepSeek返回数据异常'];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getEmbedding($text)
    {
        try {
            $response = $this->client->post('/v1/embeddings', [
                'json' => [
                    'model' => 'text-embedding-v1',
                    'input' => $text,
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['data'][0]['embedding'])) {
                return [
                    'success' => true,
                    'embedding' => $result['data'][0]['embedding'],
                ];
            }

            return ['success' => false, 'error' => 'Embedding获取失败'];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}