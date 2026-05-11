<?php

namespace app\openapi\http\middleware;

use app\common\service\JsonService;
use think\facade\Cache;
use think\Request;

class ApiAuthMiddleware
{
    private $openApiConfig = [
        'app_key' => 'jiangjunshijia_open_2026',
        'app_secret' => 'Jjsj@OpenApi#Secret!2026',
        'sign_expire' => 300,
    ];

    public function handle(Request $request, \Closure $next)
    {
        $appKey = $request->header('app-key', '');
        $timestamp = $request->header('timestamp', '');
        $sign = $request->header('sign', '');
        $nonce = $request->header('nonce', '');

        if (empty($appKey) || empty($timestamp) || empty($sign)) {
            return JsonService::fail('缺少必要认证参数(app-key/timestamp/sign)', [], 0, 0);
        }

        if ($appKey !== $this->openApiConfig['app_key']) {
            return JsonService::fail('app-key无效', [], 0, 0);
        }

        if (abs(time() - intval($timestamp)) > $this->openApiConfig['sign_expire']) {
            return JsonService::fail('请求已过期', [], 0, 0);
        }

        if (!empty($nonce)) {
            $cacheKey = 'openapi_nonce:' . $nonce;
            if (Cache::get($cacheKey)) {
                return JsonService::fail('重复请求', [], 0, 0);
            }
            Cache::set($cacheKey, 1, $this->openApiConfig['sign_expire']);
        }

        $params = $request->param();
        ksort($params);
        $paramStr = http_build_query($params);
        $signStr = $appKey . $timestamp . $paramStr . $this->openApiConfig['app_secret'];
        $expectedSign = md5($signStr);

        if (strtolower($sign) !== strtolower($expectedSign)) {
            return JsonService::fail('签名验证失败', [], 0, 0);
        }

        return $next($request);
    }
}
