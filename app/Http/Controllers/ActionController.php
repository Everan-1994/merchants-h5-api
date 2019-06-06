<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AdminPermission;
use App\Models\Action;
use App\Traits\UpdateSort;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ActionController extends Controller
{
    use UpdateSort;

    /**
     * 获取权限列表.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function lists()
    {
        $actions = (new Action())->toTree();

        return $this->success($actions);
    }

    /**
     * 添加权限.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws ValidationException
     */
    public function add(Request $request)
    {
        $params = $this->validate($request, [
            'name' => 'required|uniqueSoftDelete:actions,name',
            'route' => 'present|string',
            'parentId' => 'present|int',
            'description' => 'present|string',
        ]);

        if ($params['route'] && !in_array($params['route'], $this->getRouteList())) {
            return $this->fail(PARAM_ERROR, '路由填写错误');
        }

        $actionModel = new Action();
        $actionModel->name = $params['name'];
        $actionModel->parentId = $params['parentId'];
        $actionModel->description = $params['description'];
        $actionModel->route = $params['route'];
        $actionModel->sort = time();
        $actionModel->save();

        return $this->success();
    }

    /**
     * 更新权限.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|int',
            'name' => 'required|string',
            'description' => 'present|string',
            'sort' => 'present|int',
        ];

        // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
        $params = $this->validate($request, $rules);

        $actionData = Action::query()->find($params['id']);

        if (!$actionData) {
            return $this->fail(UNKNOWN_ACTION);
        }

        /* @var Action $actionData */
        $actionData->name = $params['name'];
        $actionData->description = $params['description'];
        $actionData->sort = $params['sort'];

        if ($actionData->update()) {
            return $this->success();
        }

        return $this->fail(UPDATE_ACTION_ERROR);
    }

    /**
     * 删除权限.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws ValidationException
     */
    public function delete(Request $request)
    {
        $params = $this->validate($request, [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|distinct|int',
        ]);

        // 初始返回数据
        $response = [
            'success' => [],
            'fail' => [],
        ];

        foreach ($params['ids'] as $id) {
            $actionData = Action::query()->where('parentId', $id)->count();
            if (!$actionData) {
                Action::query()->where('id', $id)->delete();
                $response['success'][] = $id;
            } else {
                $response['fail'][] = $id;
            }
        }

        return $this->success($response);
    }

    /**
     * 路由地图.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function route()
    {
        $parentActions = [0 => '首页'];
        $routes = array_values(
            array_diff(
                $this->getRouteList(),
                $this->getActionList($parentActions)
            )
        );

        // 过滤前端路由
        $routeList = array_diff($routes, (new AdminPermission())->publicAction);
        $route = collect($routeList)->filter(function ($value, $key) {
            if (preg_match('*:/admin/*', $value, $match)) {
                return $value;
            }
        });

        $data = [
            'parent' => $parentActions,
            'routes' => $route->all(),
        ];

        return $this->success($data);
    }

    /**
     * 获取所有路由列表.
     *
     * @return array
     */
    protected function getRouteList()
    {
        $routeMap = [];
        $routes = app()->router->getRoutes();
        foreach ($routes as $route) {
            $routeMap[] = ucfirst(strtolower($route['method'])).':'.$route['uri'];
        }

        return $routeMap;
    }

    /**
     * 获取所有已入库路由列表.
     *
     * @return array
     */
    protected function getActionList(&$parentActions = [])
    {
        $actionMap = [];
        $actions = (new Action())->allNodes();
        foreach ($actions as $action) {
            $parentId = $action['parentId'];
            if (!$parentId) {
                $parentActions[$action['id']] = $action['name'];
                continue;
            }
            $actionMap[] = $action['route'];
        }

        return $actionMap;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws ValidationException
     */
    public function sort(Request $request)
    {
        $params = $this->validate($request, [
            'item' => 'required|array|min:1',
            'sortType' => 'required|string',
        ]);

        if ($this->commonSort(
            app(Action::class),
            $params['sortType'],
            $params['item']['id'],
            $params['item']['sort'],
            'parentId',
            $params['item']['parentId']
        )) {
            return $this->success();
        }

        return $this->fail(SORT_ACTION_ERROR);
    }
}
