<?php
// 测试登录
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

$app = new think\App();
$app->initialize();

echo "=== 测试登录逻辑 ===\n\n";

// 测试参数
$account = 'test_business';
$password = '123456';
$terminal = 3;

echo "步骤1：检查账号是否存在\n";
$admin = \app\common\model\Admin::where('account', '=', $account)->find();
if ($admin) {
    echo "✓ 账号存在: {$admin->account} (ID: {$admin->id})\n";
    echo "  名称: {$admin->name}\n";
    echo "  状态: " . ($admin->disable ? '禁用' : '正常') . "\n";
    echo "  密码哈希: " . substr($admin->password, 0, 20) . "...\n";
} else {
    echo "✗ 账号不存在\n";
    exit;
}

echo "\n步骤2：验证密码\n";
$passwordSalt = \think\facade\Config::get('project.unique_identification', 'likeshop');
echo "  密码盐: {$passwordSalt}\n";
$hashedPassword = create_password($password, $passwordSalt);
echo "  输入密码哈希: {$hashedPassword}\n";
echo "  数据库密码哈希: {$admin->password}\n";

if ($admin->password === $hashedPassword) {
    echo "✓ 密码正确\n";
} else {
    echo "✗ 密码错误\n";
    exit;
}

echo "\n步骤3：创建Token\n";
try {
    $tokenResult = \app\businessapi\service\AdminTokenService::setToken($admin->id, $terminal, $admin->multipoint_login);
    if ($tokenResult) {
        echo "✓ Token创建成功!\n";
        echo "  Token: {$tokenResult['token']}\n";
        echo "  管理员ID: {$tokenResult['admin_id']}\n";
        echo "  终端: {$tokenResult['terminal']}\n";
        echo "  过期时间: " . date('Y-m-d H:i:s', $tokenResult['expire_time']) . "\n";
    } else {
        echo "✗ Token创建失败\n";
    }
} catch (Exception $e) {
    echo "✗ Token创建异常: " . $e->getMessage() . "\n";
    echo "  堆栈: " . $e->getTraceAsString() . "\n";
}

echo "\n=== 测试完成 ===\n";
