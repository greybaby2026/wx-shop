<?php
// 调试接口 - 查看请求信息
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// 获取所有请求信息
$requestInfo = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'url' => $_SERVER['REQUEST_URI'],
    'headers' => getallheaders(),
    'post_data' => $_POST,
    'get_data' => $_GET,
    'raw_input' => file_get_contents('php://input'),
    'server' => [
        'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'N/A',
        'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
    ]
];

echo json_encode($requestInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
