<?php

return [
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],
    'guards' => [
        'api' => [
            'driver' => 'jwt',    //### 更改为JWT驱动
            'provider' => 'admin',
        ],
        'user' => [
            'driver' => 'jwt',   // 结合扩展这里定义即生效
            'provider' => 'user',
        ],
    ],
    'providers' => [
        'admin' => [
            'driver' => 'eloquent',
            'model' => \App\Models\AdminUser::class,        //### 指定用于token验证的模型类
        ],
        'user' => [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class,        //### 指定用于token验证的模型类
        ],
    ],
    'passwords' => [ //### Lumen默认无session，所以该字段无意义
    ],
];
