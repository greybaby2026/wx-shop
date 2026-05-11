<?php
/**
 * 测试请求头接收
 */

header('Content-Type: application/json');

$headers = getallheaders();

$response = [
    'code' => 1,
    'msg' => '请求头测试',
    'data' => [
        'all_headers' => $headers,
        'token_from_header' => $headers['token'] ?? '未找到',
        'Token_from_header' => $headers['Token'] ?? '未找到',
        'HTTP_TOKEN' => $_SERVER['HTTP_TOKEN'] ?? '未找到',
        'request_method' => $_SERVER['REQUEST_METHOD'] ?? '未知',
        'request_uri' => $_SERVER['REQUEST_URI'] ?? '未知',
    ]
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
