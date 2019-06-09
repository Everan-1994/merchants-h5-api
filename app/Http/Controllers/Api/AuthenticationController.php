<?php

namespace App\Http\Controllers\Api;

use Laravel\Lumen\Routing\Controller;

class AuthenticationController extends Controller
{
    protected $app;

    public function __construct()
    {
        $this->app = app('wechat.official_account');
    }

    public function server()
    {
        $this->app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'text':
                    return '收到文字消息：' . $message['Content'];
                    break;
                default:
                    return '欢迎关注 『Merchants』！';
                    break;
            }
        });

        return $this->app->server->serve();
    }

    public function oauth()
    {
        $response = $this->app->oauth->scopes(['snsapi_userinfo'])->redirect(env('APP_URL') . '/api/user');

        return $response;
    }

    public function user()
    {
        $user = $this->app->oauth->user();
        dd($user);
    }

}