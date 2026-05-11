<?php
// 创建测试账号
require __DIR__ . '/../vendor/autoload.php';

$app = new think\App();
$app->initialize();

// 配置
$account = 'test_business';
$password = '123456';
// 使用系统自带的create_password函数
$passwordSalt = \think\facade\Config::get('project.unique_identification', 'likeshop');
$hashedPassword = create_password($password, $passwordSalt);

echo "=== 创建商家端测试账号 ===\n";
echo "账号: {$account}\n";
echo "密码: {$password}\n";
echo "加密后密码: {$hashedPassword}\n\n";

// 检查账号是否存在
$admin = \app\common\model\Admin::where('account', $account)->find();

if ($admin) {
    echo "账号已存在，更新密码...\n";
    $admin->password = $hashedPassword;
    $admin->disable = 0;
    $admin->save();
    echo "✓ 密码已更新！\n";
    $adminId = $admin->id;
} else {
    echo "创建新账号...\n";
    $admin = \app\common\model\Admin::create([
        'account' => $account,
        'password' => $hashedPassword,
        'name' => '测试商家',
        'disable' => 0,
        'multipoint_login' => 1,
        'role_id' => 1,
    ]);
    echo "✓ 账号创建成功！ID: {$admin->id}\n";
    $adminId = $admin->id;
}

// 创建token
$token = create_token((string)$adminId);
$expireTime = time() + 86400 * 7; // 7天

echo "\n=== 创建Token ===\n";
echo "Token: {$token}\n";
echo "过期时间: " . date('Y-m-d H:i:s', $expireTime) . "\n";

// 检查是否已有terminal=3的token
$session = \app\common\model\AdminSession::where([
    ['admin_id', '=', $adminId],
    ['terminal', '=', 3]
])->find();

if ($session) {
    echo "\n更新现有token...\n";
    $session->token = $token;
    $session->expire_time = $expireTime;
    $session->save();
    echo "✓ Token已更新！\n";
} else {
    echo "\n创建新token记录...\n";
    \app\common\model\AdminSession::create([
        'admin_id' => $adminId,
        'terminal' => 3,
        'token' => $token,
        'expire_time' => $expireTime
    ]);
    echo "✓ Token记录创建成功！\n";
}

// 清除缓存
$cache = new \app\common\cache\AdminTokenCache();
$cache->setAdminInfo($token);

echo "\n=== 测试完成 ===\n";
echo "\n请使用以下信息登录商家端小程序：\n";
echo "账号: {$account}\n";
echo "密码: {$password}\n";
echo "Token: {$token}\n";
