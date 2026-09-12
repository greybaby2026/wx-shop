<?php

namespace app\openapi\http\middleware;

use app\common\service\JsonService;
use think\facade\Cache;
use think\Request;

class ApiAuthMiddleware
{
    /**
     * OpenAPI 鉴权配置
     *
     * 凭据一律从 server/.env 的 [OPENAPI] 段读取，代码中不再保留任何明文密钥。
     * 泄露后的轮换步骤：改 .env 中 APP_KEY / APP_SECRET，然后同步调用方配置。
     */
    private function getConfig(): array
    {
        $appKey    = (string)env('OPENAPI.APP_KEY', '');
        $appSecret = (string)env('OPENAPI.APP_SECRET', '');

        return [
            'app_key'     => $appKey,
            'app_secret'  => $appSecret,
            'sign_expire' => 300,
        ];
    }

    public function handle(Request $request, \Closure $next)
    {
        $config = $this->getConfig();

        // 凭据未配置时直接拒绝，避免空密钥被绕过
        if ($config['app_key'] === '' || $config['app_secret'] === '') {
            return JsonService::fail('OpenAPI 凭据未配置', [], 0, 0);
        }

        $appKey = $request->header('app-key', '');
        $timestamp = $request->header('timestamp', '');
        $sign = $request->header('sign', '');
        $nonce = $request->header('nonce', '');

        if (empty($appKey) || empty($timestamp) || empty($sign)) {
            return JsonService::fail('缺少必要认证参数(app-key/timestamp/sign)', [], 0, 0);
        }

        // 使用恒定时间比较，避免时序侧信道
        if (!hash_equals($config['app_key'], (string)$appKey)) {
            return JsonService::fail('app-key无效', [], 0, 0);
        }

        if (abs(time() - intval($timestamp)) > $config['sign_expire']) {
            return JsonService::fail('请求已过期', [], 0, 0);
        }

        if (!empty($nonce)) {
            $cacheKey = 'openapi_nonce:' . $nonce;
            if (Cache::get($cacheKey)) {
                return JsonService::fail('重复请求', [], 0, 0);
            }
            Cache::set($cacheKey, 1, $config['sign_expire']);
        }

        $params = $request->param();
        ksort($params);
        $paramStr = http_build_query($params);
        $signStr = $config['app_key'] . $timestamp . $paramStr . $config['app_secret'];
        $expectedSign = md5($signStr);

        if (!hash_equals($expectedSign, strtolower((string)$sign))) {
            return JsonService::fail('签名验证失败', [], 0, 0);
        }

        return $next($request);
    }
}
