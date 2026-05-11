<?php

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动
    'default' => 'file',

    // 缓存连接方式配置
    'stores'  => [
        'file' => [
            // 驱动方式
            'type'       => 'File',
            // 缓存保存目录 - 使用已存在的08目录
            'path'       => runtime_path() . 'cache' . DIRECTORY_SEPARATOR . '08',
            // 缓存前缀
            'prefix'     => '',
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            // 序列化机制 例如 ['serialize', 'unserialize']
            'serialize'  => [],
        ],
        // 更多的缓存连接
        'redis'   =>  [
            'prefix' => 'likeshopb2cplus-'.env('PROJECT.UNIQUE_IDENTIFICATION', '934c'),
            // 驱动方式
            'type' => 'redis',
            // 服务器地址
            'host' => env('cache.host', '127.0.0.1'),
            // 端口
            'port' => env('cache.port', '6379'),
            // 密码
            'password' => env('cache.password', ''),
        ],
    ],
];
