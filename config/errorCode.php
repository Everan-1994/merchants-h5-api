<?php

if (!defined('ERROR_CODE')) {
    define('ERROR_CODE', 999);

    // Common (10000 -10099)
    define('PARAM_ERROR', 300);
    define('TOKEN_EXPIRED', 400);
    define('UNAUTHORIZED', 401);
    define('PERMISSION_DENIED', 402);
    define('HTTP_FORBIDDEN', 403);
    define('NOT_FOUND', 404);
    define('SYSTEM_ERROR', 10000);
    define('LOGIN_ERROR', 10001);
    define('UPLOAD_ERROR', 10002);
    define('OLD_PASSWORD_ERROR', 10003);
    define('UPDATE_PASSWORD_ERROR', 10004);

    define('AP_ORDER_ERROR', 10105);

    // Role (10400 - 10499)
    define('UNKNOWN_ROLE', 10400);
    define('ADD_ROLE_ERROR', 10401);
    define('UPDATE_ROLE_ERROR', 10402);
    define('DELETE_ROLE_ERROR', 10403);

    // Member (10500 - 10599)
    define('UNKNOWN_MEMBER', 10500);
    define('ADD_MEMBER_ERROR', 10501);
    define('UPDATE_MEMBER_ERROR', 10502);
    define('DELETE_MEMBER_ERROR', 10503);
    define('MEMBER_STATUS_DISABLE', 10504);

    // Action (10700 - 10799)
    define('UNKNOWN_ACTION', 10700);
    define('ADD_ACTION_ERROR', 10701);
    define('UPDATE_ACTION_ERROR', 10702);
    define('DELETE_ACTION_ERROR', 10703);
    define('SORT_ACTION_ERROR', 10704);
}

return [
    'code' => [
        PERMISSION_DENIED => '没有权限进行此操作',
        LOGIN_ERROR => '账号密码错误',
        UPLOAD_ERROR => '上传文件不合法',
        OLD_PASSWORD_ERROR => '原密码错误',
        UPDATE_PASSWORD_ERROR => '更新用户密码失败',
        HTTP_FORBIDDEN => '没有权限',
        AP_ORDER_ERROR => '排序失败',

        /* 角色 **/
        UNKNOWN_ROLE => '角色不存在',
        ADD_ROLE_ERROR => '添加角色失败',
        UPDATE_ROLE_ERROR => '修改角色信息失败',
        DELETE_ROLE_ERROR => '删除角色失败',

        /* 成员 **/
        UNKNOWN_MEMBER => '成员不存在',
        ADD_MEMBER_ERROR => '添加成员失败',
        UPDATE_MEMBER_ERROR => '修改成员信息失败',
        DELETE_MEMBER_ERROR => '删除成员失败',
        MEMBER_STATUS_DISABLE => '未启用的账户',

        /* action **/
        UNKNOWN_ACTION => '权限不存在',
        ADD_ACTION_ERROR => '添加权限失败',
        UPDATE_ACTION_ERROR => '修改权限信息失败',
        DELETE_ACTION_ERROR => '删除权限失败',
        SORT_ACTION_ERROR => '权限排序失败'
    ],
];
