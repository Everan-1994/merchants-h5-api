<?php

namespace App\Providers;

use App\Models\OperationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Jenssegers\Agent\Agent;
use Zhuzhichao\IpLocationZh\Ip;

class OperationServiceProvide extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Agent::class, function ($app) {
            return new Agent();
        });

        $this->app->singleton(Ip::class, function ($app) {
            return new Ip();
        });
    }

    /**
     * @param Request $request
     */
    public function boot(Request $request)
    {
        register_shutdown_function([$this, 'operationShutdown'], $request);
    }

    public function operationShutdown(Request $request)
    {
        if (0 !== strpos($request->path(), 'admin')) {
            return;
        }

        $userId = Auth::id() ?? Cache::pull('userId');
        $code = Cache::pull('errorCode_' . $userId);
        $message = Cache::pull('message_' . $userId);

        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $agent = $this->app->make('Jenssegers\Agent\Agent');
            $ips = $this->app->make('Zhuzhichao\IpLocationZh\Ip');

            $userName = Auth::user()->username ?? Cache::get('userName');
            // $uri = $request->path();
            $method = $request->method();
            $userAgent = $agent->getUserAgent();
//            $ip = $request->getClientIp();
            $ip = $this->getIp();

            $ipInfo = join(array_unique(array_filter($ips->find($ip))), '-');

            // 全部路由
            $routes = app()->router->getRoutes();

            $uri = '';
            foreach ($routes as $item) {
                if (@$item['action']['uses'] == $request->route()[1]['uses'] && $item['method'] == $method) {
                    if (OperationLog::ROUTER_PATH === $item['uri']) {
                        $uri = '/' . $request->path();
                    } else {
                        $uri = $item['uri'];
                    }
                }
            }

            // 路由名称
            $where = ucfirst($method) . ':' . $uri;
            $route = DB::table('actions')->where('route', $where)->value('name');

            $data = [
                'username' => $userName,
                'uri'      => $uri,
                'route'    => $route ?? OperationLog::$routes[$uri],
                'method'   => $method,
                'agent'    => $userAgent,
                'ip'       => $ip,
                'ipInfo'   => $ipInfo,
                'code'     => $code,
                'message'  => $message,
            ];

            if ($request->all()) {
                $data['data'] = json_encode($request->all());
            }

            // 路由参数
            $arrPath = array_filter(explode('/', $request->path()));
            $arrUri = array_filter(explode('/', ltrim($uri, '/')));

            if ($arrPath !== $arrUri) {
                $arrRoute = [];
                foreach ($arrPath as $k => $item) {
                    if (!in_array($item, $arrUri)) {
                        preg_match('/\b[a-zA-Z]+\b/', $arrUri[$k], $key);
                        $arrRoute[$key[0]] = $item;
                    }
                }
                if (!empty($arrRoute)) {
                    $data['params'] = json_encode($arrRoute);
                }
            }

            OperationLog::query()->create($data);
        }
    }

    /**
     * @return array|false|string
     */
    protected function getIp()
    {
        if (isset($_SERVER['HTTP_X_REAL_IP']) && $_SERVER['HTTP_X_REAL_IP'] && strcasecmp($_SERVER['HTTP_X_REAL_IP'], 'unknown')) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], 'unknown')) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] && strcasecmp($_SERVER['HTTP_CLIENT_IP'], 'unknown')) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = 'unknown';
        }

        return $ip;
    }
}
