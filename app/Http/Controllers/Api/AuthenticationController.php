<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
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

    public function oauth(Request $request)
    {
        $response = $this->app->oauth->scopes(['snsapi_userinfo'])
            ->redirect(env('APP_URL') . '/api/oauth_callback?back_url=' . urlencode($request->fullUrl()));

        return $response;
    }

    public function oauthCallback(Request $request)
    {
        // 获取发起授权的当前页面
        $target_url = $request->exists('back_url') ? urldecode($request->input('back_url')) : '/';

        // 获取授权用户信息
        $user = $this->app->oauth->user();

        // 

        return response()->json([
            'target_url' => $target_url,
            'openid' => $user->openid,
            'openid2' => $user['openid']
        ], 200);
    }

    public function menu()
    {
        $buttons = [
            [
                "type" => "view",
                "name" => "授权页",
                "url"  => env('APP_URL') . '/api/oauth'
            ],
            [
                "type" => "view",
                "name" => "授权信息",
                "url"  =>  env('APP_URL') . '/api/user'
            ],
            [
                "name" => "其他",
                "sub_button"  => [
                    [
                        "type" => "view",
                        "name" => "送货单表",
                        "url"  => "http://www.baidu.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "个人中心",
                        "url"  => "http://www.baidu.com/"
                    ],
                ]
            ],
        ];

        $this->app->menu->create($buttons);
    }

}