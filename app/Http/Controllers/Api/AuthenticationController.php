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

    public function oauth()
    {
        $response = $this->app->oauth->scopes(['snsapi_userinfo'])->redirect(env('APP_URL') . '/api/user');

        dd($response);
    }

    public function user()
    {
        $user = $this->app->oauth->user();
        dd($user);
    }

}