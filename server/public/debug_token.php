<?php
// 调试接口 - 测试token验证
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require __DIR__ . '/../vendor/autoload.php';

$app = new think\App();
$app->initialize();

// 获取所有token记录
$sessions = \app\common\model\AdminSession::select();
$result = [];

foreach ($sessions as $session) {
    $token = $session->token;
    
    // 测试缓存
    $cache = new \app\common\cache\AdminTokenCache();
    $adminInfo = $cache->getAdminInfo($token);
    
    $result[] = [
        'admin_id' => $session->admin_id,
        'terminal' => $session->terminal,
        'token' => substr($token, 0, 20) . '...',
        'expire_time' => date('Y-m-d H:i:s', $session->expire_time),
        'is_expired' => $session->expire_time < time(),
        'cache_exists' => !empty($adminInfo),
        'cache_data' => $adminInfo ? 'YES' : 'NO'
    ];
}

echo json_encode([
    'current_time' => date('Y-m-d H:i:s'),
    'sessions' => $result
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
