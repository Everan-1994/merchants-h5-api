<?php

namespace App\Models;

class OperationLog extends BaseModel
{
    const ROUTER_PATH = '/admin/{path:.*}';
    const ROUTER_LOGOUT = '/admin/logout';
    const ROUTER_LOGIN = '/admin/login';
    const ROUTER_UPLOAD = '/admin/upload';
    const ROUTER_USER_UPDATE = '/admin/update';

    public static $routes = [
        self::ROUTER_LOGOUT => '退出登录',
        self::ROUTER_LOGIN => '登录',
        self::ROUTER_UPLOAD => '上传图片',
        self::ROUTER_USER_UPDATE => '修改个人密码',
    ];

    protected $table = 'operation_log';

    protected $fillable = [
        'username', 'agent', 'uri', 'params', 'code',
        'ip', 'ipInfo', 'method', 'data', 'route', 'message',
    ];
}
