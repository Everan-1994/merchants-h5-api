<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->group([
    'prefix' => 'admin', // 前缀
    'middleware' => ['cors'],
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

        // 用户
        $router->group(['prefix' => 'user'], function ($router) {
            /* @var \Laravel\Lumen\Routing\Router $router */
            $router->get('/', 'UserController@index');                      // 列表
            $router->patch('/{id}', 'UserController@updateStatus');         // 更新
        });

        // 反馈
        $router->group(['prefix' => 'suggest'], function ($router) {
            /* @var \Laravel\Lumen\Routing\Router $router */
            $router->get('/', 'SuggestController@index');                      // 列表
            $router->delete('/', 'SuggestController@delete');                  // 列表
        });

        // 中奖
        $router->group(['prefix' => 'winning'], function ($router) {
            /* @var \Laravel\Lumen\Routing\Router $router */
            $router->get('/', 'WinningController@index');                      // 列表
            $router->patch('/{id}', 'WinningController@updateStatus');         // 更新
        });

        // 签到规则&关于我们
        $router->group(['prefix' => 'others'], function ($router) {
            /* @var \Laravel\Lumen\Routing\Router $router */
            $router->get('/{id}', 'OtherController@show');                 // 详情
            $router->put('/{id}', 'OtherController@update');               // 更新
        });

        // 奖品
        $router->group(['prefix' => 'prizes'], function ($router) {
            /* @var \Laravel\Lumen\Routing\Router $router */

            $router->get('/', 'PrizeController@index');                    // 列表
            $router->get('/{id}', 'PrizeController@show');                 // 详情
            $router->post('/', 'PrizeController@store');                   // 新增
            $router->put('/{id}', 'PrizeController@update');               // 更新
            $router->delete('/', 'PrizeController@delete');                // 删除单个
        });

        // 活动
        $router->group(['prefix' => 'activity'], function ($router) {
            /* @var \Laravel\Lumen\Routing\Router $router */

            $router->post('/', 'ActivityController@store');              // 新增
            $router->get('/', 'ActivityController@index');               // 列表
            $router->get('/{id}', 'ActivityController@show');            // 详情
            $router->patch('/sort', 'ActivityController@updateSort');    // 更新排序
            $router->put('/{id}', 'ActivityController@update');          // 更新
            $router->delete('/', 'ActivityController@delete');           // 删除

            // 申请列表
            $router->get('/{activityId:[0-9]+}/sign', 'ActivitySignController@index'); // list
            $router->group(['prefix' => 'sign'], function ($router) {
                $router->delete('/', 'ActivitySignController@delete'); // delete
            });

            // 报告列表
            $router->get('/{activityId:[0-9]+}/report', 'ActivityReportController@index'); // list
            $router->group(['prefix' => 'report'], function ($router) {
                $router->delete('/', 'ActivityReportController@delete'); // delete
            });
        });

        // 试用
        $router->group(['prefix' => 'try_use'], function ($router) {
            /* @var \Laravel\Lumen\Routing\Router $router */

            $router->post('/', 'TryUseController@store');              // 新增
            $router->get('/', 'TryUseController@index');               // 列表
            $router->get('/{id}', 'TryUseController@show');            // 详情
            $router->patch('/sort', 'TryUseController@updateSort');    // 更新排序
            $router->put('/{id}', 'TryUseController@update');          // 更新
            $router->delete('/', 'TryUseController@delete');           // 删除

            // 申请列表
            $router->get('/{tryUseId:[0-9]+}/sign', 'TryUseSignController@index'); // list
            $router->group(['prefix' => 'sign'], function ($router) {
                $router->delete('/', 'TryUseSignController@delete'); // delete
            });

            // 报告列表
            $router->get('/{tryUseId:[0-9]+}/report', 'TryUseReportController@index'); // list
            $router->group(['prefix' => 'report'], function ($router) {
                $router->delete('/', 'TryUseReportController@delete'); // delete
            });
        });

        // 话题
        $router->group(['prefix' => 'topic'], function ($router) {
            /* @var \Laravel\Lumen\Routing\Router $router */

            $router->post('/', 'TopicController@store');              // 新增
            $router->get('/', 'TopicController@index');               // 列表
            $router->get('/{id}', 'TopicController@show');            // 详情
            $router->patch('/sort', 'TopicController@updateSort');    // 更新排序
            $router->put('/{id}', 'TopicController@update');          // 更新
            $router->delete('/', 'TopicController@delete');           // 删除

            // 评论列表
            $router->get('/{topicId:[0-9]+}/item', 'CommentController@index'); // list
            $router->group(['prefix' => 'item'], function ($router) {
                $router->delete('/', 'CommentController@delete'); // delete
            });
        });


        // 视频模块
        $router->group(['prefix' => 'block'], function ($router) {
            /* @var \Laravel\Lumen\Routing\Router $router */
            $router->get('/', 'BlockController@readAll'); // list
            $router->get('{id:[0-9]+}', 'BlockController@read'); // detail
            $router->post('/', 'BlockController@createOrUpdate'); // create
            $router->put('/{id:[0-9]+}', 'BlockController@createOrUpdate'); // update
            $router->delete('/', 'BlockController@delete'); // delete

            // 视频列表
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
    'prefix'     => 'api', // 前缀
    'namespace'  => 'Api',
    'middleware' => ['cors'],
], function ($router) {
    /* @var \Laravel\Lumen\Routing\Router $router */

    // 防止找不到 options 路由而报跨域错误
    $router->options('/{path:.*}', function ($path) {
    });

    // 微信授权
    $router->get('oauth', 'AuthenticationController@oauth');

    $router->post('login', 'AuthenticationController@login');

    // $router->get('menu', 'AuthenticationController@menu');

    // 需要授权登录才能访问
    $router->group(['middleware' => ['auth:user']], function ($router) {
        /* @var \Laravel\Lumen\Routing\Router $router */
        $router->post('check_in', 'CheckInController@add'); // 签到
        $router->get('check_in', 'CheckInController@index'); // 签到列表

        $router->post('winning_info', 'WinningController@add'); // 录入中奖信息
        $router->get('winning_info/{id}', 'WinningController@getWinningInfo'); // 中奖信息

        $router->get('activity/{id}', 'ActivityController@getActivityDetail'); // 活动详情
        $router->post('activity_sign_up', 'ActivitySignUpController@add'); // 活动报名

        $router->post('try_use', 'UseSignUpController@add'); // 试用申请
        $router->get('try_use/{id}', 'TryUseController@getTryUseDetail'); // 试用产品详情

        $router->post('report', 'ExperienceReportController@addReport'); // 填写心得
        $router->get('report/{id}', 'ExperienceReportController@getReport'); // 获取报告

        $router->post('topic/comment', 'TopicController@commentTopic'); // 评论话题
        $router->delete('topic/comment/{id}', 'TopicController@deleteTopicCommentById'); // 删除评论
        $router->get('topic/{id}', 'TopicController@getTopicDetail'); // 话题详情

        $router->patch('my/info', 'MemberController@updateUserInfo'); // 更新个人信息
        $router->get('my/report', 'MemberController@myReport'); // 我的报告
        $router->get('my/activity', 'MemberController@myActivity'); // 我的活动列表
        $router->get('my/activity/{id}', 'ActivityController@getActivityDetail'); // 我的活动
        $router->get('my/try_use', 'MemberController@myTryUse'); // 我的试用列表
        $router->get('my/try_use/{id}', 'TryUseController@getTryUseDetail'); // 我的试用详情

        $router->post('suggest', 'MemberController@submitSuggest'); // 关于我们
        $router->post('upload', 'ExperienceReportController@uploads'); // 图片上传
        $router->post('share', 'ShareZanController@share'); // 分享
        $router->post('zan', 'ShareZanController@zan'); // 点赞
    });

    $router->get('check_in_rule', 'CheckInController@checkInRule'); // 签到规则
    $router->get('about_us', 'MemberController@aboutUs'); // 关于我们
    $router->get('prize', 'PrizeController@index'); // 奖品列表
    $router->get('activity', 'ActivityController@index'); // 活动列表
    $router->get('try_use', 'TryUseController@index'); // 试用产品列表
    $router->get('topic', 'TopicController@index'); // 话题列表
    $router->get('video', 'VideoController@index'); // 视频模块列表
    $router->get('video/{id}', 'VideoController@getVideoList'); // 模块视频列表


//    $router->get('ts', function () {
//        $now = \Carbon\Carbon::now();
//        return $now->dayOfWeek;
//        return [
//            $now->copy()->subDays(6)->toDateString() . ' 00:00:00',
//            $now->copy()->toDateString() . ' 23:59:59'
//        ];
//    });

});