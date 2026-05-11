<?php
/**
 * 清除登录状态API
 * 用于强制清除所有登录相关的缓存和cookie
 */

// 清除所有Cookie
if (isset($_COOKIE)) {
    foreach ($_COOKIE as $name => $value) {
        setcookie($name, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
}

// 清除PHP session
session_start();
$_SESSION = array();
session_destroy();

// 返回JSON响应
header('Content-Type: application/json');
echo json_encode([
    'code' => 1,
    'msg' => '登录状态已清除',
    'data' => [
        'time' => date('Y-m-d H:i:s'),
        'action' => 'clear_login'
    ]
]);
