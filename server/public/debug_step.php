<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== 步骤1：检查PHP版本 ===\n";
echo "PHP版本: " . PHP_VERSION . "\n";
echo "状态: ✓ 正常\n\n";

echo "=== 步骤2：检查自动加载 ===\n";
require __DIR__ . '/../vendor/autoload.php';
echo "自动加载: ✓ 正常\n\n";

echo "=== 步骤3：检查数据库连接 ===\n";
try {
    $config = include __DIR__ . '/../config/database.php';
    $dsn = "mysql:host={$config['connections']['mysql']['hostname']};port={$config['connections']['mysql']['hostport']};dbname={$config['connections']['mysql']['database']};charset={$config['connections']['mysql']['charset']}";
    $pdo = new PDO($dsn, $config['connections']['mysql']['username'], $config['connections']['mysql']['password']);
    echo "数据库连接: ✓ 正常 ({$config['connections']['mysql']['database']})\n";
} catch (Exception $e) {
    echo "数据库连接: ✗ 失败 - " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== 步骤4：检查缓存目录 ===\n";
$cacheDir = '/tmp/likeshop_cache/';
if (is_dir($cacheDir)) {
    echo "缓存目录: ✓ 存在 ({$cacheDir})\n";
    if (is_writable($cacheDir)) {
        echo "缓存目录权限: ✓ 可写\n";
    } else {
        echo "缓存目录权限: ✗ 不可写\n";
    }
} else {
    echo "缓存目录: ✗ 不存在 ({$cacheDir})\n";
}
echo "\n";

echo "=== 步骤5：检查ThinkPHP初始化 ===\n";
try {
    $app = new think\App();
    $app->initialize();
    echo "ThinkPHP初始化: ✓ 正常 (版本: " . $app->version() . ")\n";
} catch (Exception $e) {
    echo "ThinkPHP初始化: ✗ 失败 - " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== 步骤6：检查缓存配置 ===\n";
try {
    $cacheConfig = include __DIR__ . '/../config/cache.php';
    echo "默认缓存驱动: " . $cacheConfig['default'] . "\n";
    echo "缓存路径: " . ($cacheConfig['stores']['file']['path'] ?? '未设置') . "\n";
    echo "缓存配置: ✓ 正常\n";
} catch (Exception $e) {
    echo "缓存配置: ✗ 失败 - " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== 步骤7：测试缓存写入 ===\n";
try {
    $app = new think\App();
    $app->initialize();
    $cache = think\facade\Cache::store('file');
    $cache->set('test_key', 'test_value', 60);
    $value = $cache->get('test_key');
    if ($value === 'test_value') {
        echo "缓存测试: ✓ 正常 (写入和读取成功)\n";
    } else {
        echo "缓存测试: ✗ 失败 (读取值不匹配)\n";
    }
} catch (Exception $e) {
    echo "缓存测试: ✗ 失败 - " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== 步骤8：检查Admin模型 ===\n";
try {
    $admin = \app\common\model\Admin::where('account', 'admin')->find();
    if ($admin) {
        echo "Admin模型: ✓ 正常 (找到账号: {$admin->account})\n";
    } else {
        echo "Admin模型: ⚠ 未找到admin账号\n";
    }
} catch (Exception $e) {
    echo "Admin模型: ✗ 失败 - " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== 调试完成 ===\n";
