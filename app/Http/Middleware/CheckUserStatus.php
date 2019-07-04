<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{

    public function handle($request, Closure $next)
    {
        $user_id = Auth::guard('user')->user()->id;

        if (!User::query()->whereId($user_id)->value('status')) {
            Auth::guard('user')->logout(); // 退出
            return response([
                'errorCode' => 2,
                'message'   => '账号已冻结',
            ]);
        }

        return $next($request);
    }
}