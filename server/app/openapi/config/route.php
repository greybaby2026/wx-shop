<?php

return [
    'middleware' => [
        app\openapi\http\middleware\InitMiddleware::class,
        app\openapi\http\middleware\ApiAuthMiddleware::class,
    ],
];
