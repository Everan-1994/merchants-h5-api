<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class Authenticate extends BaseMiddleware
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param \Illuminate\Contracts\Auth\Factory $auth
     *
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try {
            $this->auth->guard($guard)->getPayload();

            return $next($request);
        } catch (TokenBlacklistedException $exception) {
            throw new UnauthorizedHttpException('jwt-auth', '您已退出，请重新登录');
        } catch (TokenExpiredException $exception) {
            try {
                $token = $this->auth->guard($guard)->refresh();
                $this->auth->guard($guard)->onceUsingId($this->auth->guard($guard)->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray()['sub']);

                return $this->setAuthenticationHeader($next($request), $token);
            } catch (JWTException $exception) {
                throw new UnauthorizedHttpException('jwt-auth', $exception->getMessage());
            }
        }
    }
}
