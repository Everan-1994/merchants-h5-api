<?php

namespace App\Http\Controllers\Api;

use App\Events\UserLoginEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            ->redirect(urldecode($request->input('back_url')));

        return $response;
    }

    public function login(Request $request)
    {
        // 获取授权用户信息
        if (env('APP_ENV') == 'local') {
            $wx_user = [
                'nickname'   => 'Everan',
                'sex'        => 1,
                'headimgurl' => 'http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLibVib2v4eHlYXxlz1jgknJmoMoSNCnKhC6wkY7Eh2JicJ4RK5ygovPNG8T0WjxYz2UyaV1jID3ltow/132',
                'openid'     => 'oT9bx0xpgi1l1NJNPgtyvDKDfL1Q',
            ];
        } else {
            if (!$request->exists('code')) {
                return response()->json([
                    'errorCode' => 1,
                    'message'   => 'code 参数 缺失',
                ]);
            }

            $code = $request->input('code');

            $access_token = $this->app->oauth->getAccessToken($code);

            $wx_user = $this->app->oauth->user($access_token)->getOriginal();
        }

        // 找到 openid 对应的用户 找不到则创建
        $user = User::query()->where('openid', $wx_user['openid'])->first();

        if (!optional($user)->id) {
            $user = User::query()->create(
                [
                    'name'   => $wx_user['nickname'],
                    'sex'    => $wx_user['sex'],
                    'avatar' => $wx_user['headimgurl'],
                    'openid' => $wx_user['openid'],
                    'status' => User::ACTIVE,
                ]
            );

        }

        if (User::FREEZE === $user['status']) {
            $this->logout();

            return response()->json([
                'errorCode' => 1,
                'message'   => '账号已被冻结，请联系管理员。',
            ]);
        }

        // 给用户授权 token
        $token = Auth::guard('user')->fromUser($user);

        event(new UserLoginEvent($user['id'])); // 添加日志记录事件

        return response()->json([
            'errorCode' => 0,
            'message'   => 'success',
            'data'      => [
                'user'       => [
                    'name'   => $user['name'],
                    'sex'    => $user['sex'],
                    'avatar' => $user['avatar'],
                    'openid' => $user['openid'],
                ],
                'token'      => $this->respondWithToken($token),
            ],
        ]);
    }

    protected function respondWithToken($token)
    {
        return [
            'accessToken' => 'Bearer ' . $token,
            'expiresIn'   => Auth::guard('user')->factory()->getTTL() * 60,
        ];
    }

    public function logout()
    {
        Auth::guard('user')->logout();

        return response()->json([
            'errorCode' => 0,
            'message'   => '退出成功',
        ]);
    }

    public function menu()
    {
        $buttons = [
            [
                "type" => "view",
                "name" => "授权页",
                "url"  => env('APP_URL') . '/api/oauth',
            ],
            [
                "type" => "view",
                "name" => "授权信息",
                "url"  => env('APP_URL') . '/api/user',
            ],
            [
                "name"       => "其他",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "送货单表",
                        "url"  => "http://www.baidu.com/",
                    ],
                    [
                        "type" => "view",
                        "name" => "个人中心",
                        "url"  => "http://www.baidu.com/",
                    ],
                ],
            ],
        ];

        $this->app->menu->create($buttons);
    }

}