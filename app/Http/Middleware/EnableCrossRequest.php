<?php

namespace App\Http\Middleware;

use Closure;

class EnableCrossRequest
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
        $allow_origin = config('allowOriginHost');

        if (in_array($origin, $allow_origin)) {
            $IlluminateResponse = 'Illuminate\Http\Response';
            $SymfonyResopnse = 'Symfony\Component\HttpFoundation\Response';
            $headers = [
                'Access-Control-Allow-Origin' => $origin,
                'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
                'Access-Control-Allow-Headers' => 'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN, X_Requested_With',
                'Access-Control-Expose-Headers' => 'Authorization, authenticated',
                'Access-Control-Allow-Credentials' => 'true',
            ];

            if ($response instanceof $IlluminateResponse) {
                foreach ($headers as $key => $value) {
                    $response->header($key, $value);
                }
                return $response;
            }

            if ($response instanceof $SymfonyResopnse) {
                foreach ($headers as $key => $value) {
                    $response->headers->set($key, $value);
                }
                return $response;
            }
        }

        return $response;
    }
}
