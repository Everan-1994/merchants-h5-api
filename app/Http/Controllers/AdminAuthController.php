<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminUserResource;
use App\Models\Action;
use App\Models\AdminRole;
use App\Models\AdminUser;
use App\Models\Article;
use App\Models\SiteDistrictCount;
use App\Models\SiteUrlTrack;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * 用户登录.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $params = $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string|between:5,20',
        ]);

        $token = Auth::guard('api')->attempt([
            'email' => $params['email'],
            'password' => $params['password'],
        ]);

        if (!$token) {
            return $this->fail(LOGIN_ERROR);
        }

        // 记录登入日志
        // event(new LoginEvent(\Auth::guard('api')->user(), new Agent(), $request->getClientIp()));

        // 使用 Auth 登录用户
        $userData = (new AdminUserResource(Auth::guard('api')->user()));

        // 用户是否启用
        if (!$userData['isEnable']) {
            return $this->fail(MEMBER_STATUS_DISABLE);
        }

        $auth = [
            'userInfo' => $userData,
            'meta' => [
                'accessToken' => $token,
                'tokenType' => 'Bearer',
                'expiresIn' => Auth::guard('api')->factory()->getTTL() * 60,
            ],
            'access' => $this->getActionsByUserId($userData['id']),
        ];

        return $this->success($auth);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function accessList(Request $request)
    {
        $userData = $request->user();

        return $this->success(['accessList' => $this->getActionsByUserId($userData['id'])]);
    }

    /**
     * 查询用户所能访问的所有路由.
     *
     * @param int $userId
     *
     * @return array
     */
    public function getActionsByUserId($userId)
    {
        // 查询当前登录用户角色
        $adminUserRoles = AdminUser::query()
            ->with('roles')
            ->where('id', $userId)
            ->first()
            ->toArray();

        $userActions = [];
        $actions = [];

        // 查询各角色所拥有的权限
        if ($adminUserRoles['roles']) {
            foreach ($adminUserRoles['roles'] as $role) {
                if ($role['isSuper']) {
                    $actions[] = '*';
                    break;
                }

                $roleActions = AdminRole::query()
                    ->with('actions')
                    ->where('id', $role['id'])
                    ->first()
                    ->toArray();
                $userActions = array_merge($userActions, $roleActions['actions']);
            }

            if (!$actions) {
                foreach ($userActions as $action) {
                    if (!$action['parentId'] || !$action['route']) {
                        $this->getActionsByParentId($action['id'], $actions);
                    } else {
                        $actions[] = $action['route'];
                    }
                }
            }
        }

        return $actions;
    }

    /**
     * @param int   $parentId
     * @param array $actions
     */
    protected function getActionsByParentId($parentId, &$actions)
    {
        $actionsData = Action::query()->where('parentId', $parentId)->get()->toArray();
        if ($actionsData) {
            foreach ($actionsData as $actionData) {
                $actions[] = $actionData['route'];
            }
        }
    }

    /**
     * 首页看板数据接口.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function board()
    {
        // 文章总数
        $articleCount = Article::query()->count();

        // 今日新增文章数
        $todayArticleCount = Article::query()->where('createdAt', '>=', date('Y-m-d'))->count();

        // 总访问次数
        $pvCount = SiteDistrictCount::query()->sum('pv');

        // 今日访问次数
        $todayPvCount = SiteDistrictCount::query()->where('day', date('Ymd'))->sum('pv');

        // 最近七天起始时间
        $last7Day = date('Ymd', time() - 7 * 24 * 3600);

        // 地区浏览排行
        $topDistrict = SiteDistrictCount::query()
            ->where('day', '>=', $last7Day)
            ->select('district', DB::raw('SUM(pv) as pvCount'))
            ->orderBy('pvCount', 'desc')
            ->groupBy('district')
            ->get()
            ->toArray();

        // 页面浏览排行
        $topLink = SiteUrlTrack::query()
            ->where('day', '>=', $last7Day)
            ->select('url', DB::raw('SUM(pv) as pvCount'))
            ->orderBy('pvCount', 'desc')
            ->groupBy('url')
            ->limit(10)
            ->get()
            ->toArray();

        return $this->success([
            'articleCount' => (int) $articleCount,
            'todayArticleCount' => (int) $todayArticleCount,
            'pvCount' => (int) $pvCount,
            'todayPvCount' => (int) $todayPvCount,
            'topDistrict' => $topDistrict,
            'topLink' => $topLink,
        ]);
    }

    /**
     * 修改密码
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws ValidationException
     */
    public function update(Request $request)
    {
        $params = $this->validate($request, [
            'oldPass' => 'required|string|between:5,20',
            'newPass' => 'required|string|between:5,20',
        ]);

        $currentUser = $request->user();
        $userData = AdminUser::query()->find($currentUser['id']);

        if (!Hash::check($params['oldPass'], $userData->password)) {
            return $this->fail(OLD_PASSWORD_ERROR);
        }

        if (AdminUser::query()
            ->where('id', $currentUser['id'])
            ->update([
                'password' => app('hash')->make($params['newPass']),
            ])
        ) {
            return $this->success();
        }

        return $this->fail(UPDATE_PASSWORD_ERROR);
    }

    /**
     * 获取当前用户信息.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request)
    {
        $userData = $request->user()->toArray();

        // 查询当前登录用户角色
        $adminUserRoles = AdminUser::query()
            ->with('roles')
            ->where('id', $userData['id'])
            ->first()
            ->toArray();

        return $this->success($adminUserRoles);
    }

    /**
     * 退出登录.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $expiresAt = Carbon::now()->addMinute();

        Cache::put('userId', Auth::id(), $expiresAt);
        Cache::put('userName', Auth::user()->username, $expiresAt);

        Auth::guard('api')->logout();

        return $this->success();
    }
}
