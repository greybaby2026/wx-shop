<?php
// 调试 token 验证
header('Content-Type: text/plain; charset=utf-8');

$rootPath = dirname(__DIR__);
require $rootPath . '/vendor/autoload.php';

// 初始化应用
$http = \think\Http::run($rootPath);

use app\common\cache\AdminTokenCache;

echo "=== 调试 Token 验证 ===\n\n";

// 测试 token
$token = '4a9aa90b0c120b693947b24ef1a1b3e6';

echo "1. 检查缓存配置\n";
$cache = \think\facade\Cache::getStore();
echo "   缓存驱动: " . get_class($cache) . "\n\n";

echo "2. 检查 AdminTokenCache\n";
$adminTokenCache = new AdminTokenCache();

echo "3. 获取 AdminInfo\n";
try {
    $adminInfo = $adminTokenCache->getAdminInfo($token);
    if ($adminInfo) {
        echo "   ✓ 成功获取管理员信息\n";
        echo "   admin_id: " . $adminInfo['admin_id'] . "\n";
        echo "   name: " . $adminInfo['name'] . "\n";
        echo "   account: " . $adminInfo['account'] . "\n";
        echo "   terminal: " . $adminInfo['terminal'] . "\n";
        echo "   expire_time: " . date('Y-m-d H:i:s', $adminInfo['expire_time']) . "\n";
        echo "   是否过期: " . ($adminInfo['expire_time'] < time() ? '是' : '否') . "\n";
    } else {
        echo "   ✗ 获取失败，返回空\n";
    }
} catch (\Exception $e) {
    echo "   ✗ 异常: " . $e->getMessage() . "\n";
    echo "   文件: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n4. 直接查询数据库\n";
try {
    $session = \app\common\model\AdminSession::where('token', $token)->find();
    if ($session) {
        echo "   ✓ 数据库中找到 session\n";
        echo "   admin_id: " . $session->admin_id . "\n";
        echo "   terminal: " . $session->terminal . "\n";
        echo "   expire_time: " . date('Y-m-d H:i:s', $session->expire_time) . "\n";
        echo "   是否过期: " . ($session->expire_time < time() ? '是' : '否') . "\n";
    } else {
        echo "   ✗ 数据库中未找到 session\n";
    }
} catch (\Exception $e) {
    echo "   ✗ 异常: " . $e->getMessage() . "\n";
}

echo "\n=== 调试完成 ===\n";
