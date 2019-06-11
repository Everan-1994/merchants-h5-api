<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->group([
    'prefix' => 'admin', // 前缀
], function ($router) {
    /* @var \Laravel\Lumen\Routing\Router $router */

    // 防止找不到 options 路由而报跨域错误
    $router->options('/{path:.*}', function ($path) {
    });

    // 管理员登录
    $router->post('login', 'AdminAuthController@login');

    // 上传图片
    $router->post('upload', 'UploadFile');

    // 需要 token 验证的接口
    $router->group(['middleware' => ['refresh.token', 'permission']], function ($router) {
        /* @var \Laravel\Lumen\Routing\Router $router */

        // 可访问权限列表
        $router->get('accessList', 'AdminAuthController@accessList');

        // 后端首页看板数据接口
        $router->get('board', 'AdminAuthController@board');

        // 退出登陆
        $router->delete('logout', 'AdminAuthController@logout');

        // 获取当前登录用户信息
        $router->get('info', 'AdminAuthController@info');

        // 修改当前登录用户密码
        $router->put('update', 'AdminAuthController@update');

        // 操作日志
        $router->get('operation', 'OperationController@index');

        // 路由
        $router->group(['prefix' => 'actions'], function ($router) {
            /* @var \Laravel\Lumen\Routing\Router $router */

            $router->get('/', 'ActionController@lists');                // 列表
            $router->post('/', 'ActionController@add');                 // 新增
            $router->put('/', 'ActionController@update');               // 更新
            $router->delete('/', 'ActionController@delete');            // 删除
            $router->get('/route', 'ActionController@route');           // Map
            $router->patch('/sort', 'ActionController@sort');           // 排序
        });

        // 角色
        $router->group(['prefix' => 'roles'], function ($router) {
            /* @var \Laravel\Lumen\Routing\Router $router */

            $router->get('/', 'RoleController@lists');                  // 列表
            $router->get('/{id:[0-9]+}', 'RoleController@detail');      // 详情
            $router->post('/', 'RoleController@add');                   // 新增
            $router->put('/', 'RoleController@update');                 // 更新
            $router->delete('/', 'RoleController@delete');              // 删除
        });

        // 成员
        $router->group(['prefix' => 'members'], function ($router) {
            /* @var \Laravel\Lumen\Routing\Router $router */

            $router->get('/', 'MemberController@lists');                    // 列表
            $router->get('/{id}', 'MemberController@detail');               // 详情
            $router->post('/', 'MemberController@add');                     // 新增
            $router->put('/', 'MemberController@update');                   // 更新
            $router->delete('/', 'MemberController@delete');                // 删除单个
            $router->patch('/', 'MemberController@changeStatus');           // 变更状态
        });

        // 区块
        $router->group(['prefix' => 'block'], function ($router) {
            /* @var \Laravel\Lumen\Routing\Router $router */
            $router->get('/', 'BlockController@readAll'); // list
            $router->get('{id:[0-9]+}', 'BlockController@read'); // detail
            $router->post('/', 'BlockController@createOrUpdate'); // create
            $router->put('/{id:[0-9]+}', 'BlockController@createOrUpdate'); // update
            $router->delete('/', 'BlockController@delete'); // delete

            // 区块内容
            $router->get('/{blockId:[0-9]+}/item', 'BlockItemController@readAll'); // list
            $router->group(['prefix' => 'item'], function ($router) {
                $router->get('{id:[0-9]+}', 'BlockItemController@read'); // detail
                $router->post('/', 'BlockItemController@createOrUpdate'); // create
                $router->put('/{id:[0-9]+}', 'BlockItemController@createOrUpdate'); // update
                $router->delete('/', 'BlockItemController@delete'); // delete
                $router->patch('/sort', 'BlockItemController@sort'); // sort
            });
        });
    });
});

// 微信认证
$router->get('wechat', 'Api\AuthenticationController@server');
$router->post('wechat', 'Api\AuthenticationController@server');

/* @var \Laravel\Lumen\Routing\Router $router */
$router->group([
    'prefix' => 'api', // 前缀
    'namespace' => 'Api',
    'middleware' => ['cors']
], function ($router) {
    /* @var \Laravel\Lumen\Routing\Router $router */

    // 防止找不到 options 路由而报跨域错误
    $router->options('/{path:.*}', function ($path) {
    });

    // 微信授权
    $router->get('oauth', 'AuthenticationController@oauth');

    $router->get('login', 'AuthenticationController@login');

    // $router->get('menu', 'AuthenticationController@menu');

    // 需要授权才能访问
    $router->group(['middleware' => ['auth:user']], function ($router) {
        /* @var \Laravel\Lumen\Routing\Router $router */
        $router->post('check_in', 'CheckInController@add'); // 签到
        $router->get('check_in', 'CheckInController@index'); // 签到列表
    });

    $router->get('check_in_rule', 'CheckInController@checkInRule'); // 签到规则
    $router->get('prize', 'PrizeController@index'); // 奖品列表

//    $router->get('ts', function () {
//        echo \Carbon\Carbon::parse('2019-06-01')->subDay()->toDateString();
//    });

});