<?php
require __DIR__ . '/../vendor/autoload.php';

use app\common\model\User;
use app\common\service\UserTokenService;

echo "=== 创建商家端小程序测试账号 (C端用户) ===\n\n";

// 检查用户是否存在
$user = User::where('account', 'test_mp_user')->find();

if (!$user) {
    // 创建测试用户
    $user = new User();
    $user->account = 'test_mp_user';
    $user->password = '123456';
    $user->nickname = '测试商家用户';
    $user->sex = 1;
    $user->status = 1;
    $user->create_time = time();
    $user->save();
    echo "✓ 测试用户创建成功\n";
} else {
    echo "✓ 测试用户已存在\n";
}

$userId = $user->id;
echo "用户ID: $userId\n\n";

// 生成 token（小程序端 terminal=3）
$terminal = 3; // 微信小程序
$tokenInfo = UserTokenService::createToken($userId, $terminal);

if ($tokenInfo) {
    echo "✓ Token生成成功\n";
    echo "Token: " . $tokenInfo['token'] . "\n";
    echo "过期时间: " . date('Y-m-d H:i:s', $tokenInfo['expire_time']) . "\n";
} else {
    echo "✗ Token生成失败\n";
}

echo "\n=== 测试完成 ===\n";
echo "\n请使用以下信息登录商家端小程序：\n";
echo "账号: test_mp_user\n";
echo "密码: 123456\n";
echo "Terminal: $terminal (微信小程序)\n";
