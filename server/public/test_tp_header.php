<?php
require __DIR__ . '/../vendor/autoload.php';

$app = new think\App();
$app->initialize();

$request = $app->request;

header('Content-Type: application/json');

$response = [
    'code' => 1,
    'msg' => 'ThinkPHP请求头测试',
    'data' => [
        'all_headers' => $request->header(),
        'token_from_header' => $request->header('token') ?? '未找到',
        'Token_from_header' => $request->header('Token') ?? '未找到',
        'HTTP_TOKEN' => $_SERVER['HTTP_TOKEN'] ?? '未找到',
        'request_method' => $request->method(),
        'request_uri' => $request->uri(),
    ]
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
