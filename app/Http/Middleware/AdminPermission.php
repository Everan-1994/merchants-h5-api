<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AdminAuthController;
use Closure;
use Illuminate\Validation\UnauthorizedException;

class AdminPermission
{
    public $publicAction = [
        'Options:/admin/{path:.*}',
        'Post:/admin/login',
        'Delete:/admin/logout',
        'Get:/admin/info',
        'Put:/admin/update',
        'Get:/admin/accessList',
        'Get:/admin/board',
        'Get:/admin/actions/route',
        'Post:/admin/upload',
    ];

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
        $userData = $request->user();

        $actions = ((new AdminAuthController()))->getActionsByUserId($userData['id']);

        if ($actions != ['*']) {
            $actions = array_merge($actions, $this->publicAction);

            $routes = app()->router->getRoutes();

            $route = [];

            foreach ($routes as $item) {
                if (@$item['action']['uses'] == $request->route()[1]['uses']) {
                    $method = ucfirst(strtolower($item['method']));
                    $route[] = $method.':'.$item['uri'];
                }
            }

            if ($route) {
                if (!array_intersect($route, $actions)) {
                    throw new UnauthorizedException('没有权限');
                }
            }
        }

        return $next($request);
    }
}
