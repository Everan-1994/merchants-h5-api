<?php

namespace App\Http\Controllers;

use App\Models\AdminRole;
use App\Models\AdminRoleAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    /**
     * 获取角色列表及拥有角色的成员数量.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function lists()
    {
        $roles = AdminRole::all();
        $data = [];
        foreach ($roles as $role) {
            /* @var AdminRole $role */
            $data[] = [
                'id' => $role->id,
                'name' => $role->name,
                'memberCount' => $role->adminUsers()->count(),
                'createdAt' => $role->createdAt->toDateTimeString(),
            ];
        }

        return $this->success($data);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail($id)
    {
        $roleData = AdminRole::query()->find($id);

        if (!$roleData) {
            return $this->fail(UNKNOWN_ROLE);
        }

        /** @var AdminRole $roleData */
        $roleActions = $roleData->adminRoleActions()->get()->toArray();
        $actions = [];
        foreach ($roleActions as $item) {
            $actions[] = $item['actionId'];
        }

        $response = [
            'id' => $roleData->id,
            'name' => $roleData->name,
            'actions' => $actions,
        ];

        return $this->success($response);
    }

    /**
     * 添加角色.
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
            'name' => 'required|uniqueSoftDelete:admin_roles,name',
            'actions' => 'present|array|min:1',
            'actions.*' => 'required|distinct|int',
        ]);

        // 开启事务
        DB::beginTransaction();

        // 保存角色信息
        $roleModel = new AdminRole();
        $roleModel->name = $params['name'];
        if ($roleModel->save()) {
            // 保存角色对应的权限信息
            $roleActions = [];
            foreach ($params['actions'] as $action) {
                $roleActionsModel = new AdminRoleAction();
                $roleActionsModel->actionId = $action;
                $roleActions[] = $roleActionsModel;
            }
            $roleModel->adminRoleActions()->saveMany($roleActions);

            DB::commit();

            return $this->success();
        } else {
            DB::rollBack();
        }

        return $this->fail(ADD_ROLE_ERROR);
    }

    /**
     * 更新角色信息.
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
            'id' => 'required|int',
            'name' => 'required|string',
            'actions' => 'present|array|min:1',
            'actions.*' => 'required|distinct|int',
        ]);

        $roleData = AdminRole::query()->find($params['id']);
        if (!$roleData) {
            return $this->fail(UNKNOWN_ROLE);
        }

        // 开启事务
        DB::beginTransaction();

        // 更新角色信息
        /** @var AdminRole $roleData */
        if ($roleData->update(['name' => $params['name']])) {
            // 更新角色权限信息
            $roleData->adminRoleActions()->delete();

            $roleActions = [];
            foreach ($params['actions'] as $action) {
                $roleActionsModel = new AdminRoleAction();
                $roleActionsModel->actionId = $action;
                $roleActions[] = $roleActionsModel;
            }
            $roleData->adminRoleActions()->saveMany($roleActions);

            DB::commit();

            return $this->success();
        } else {
            DB::rollBack();
        }

        return $this->fail(UPDATE_ROLE_ERROR);
    }

    /**
     * 删除角色.
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

        $rolesData = AdminRole::query()->findMany($params['ids']);
        if (!$rolesData) {
            return $this->fail(UNKNOWN_ROLE);
        }

        // 初始返回数据
        $response = [
            'success' => [],
            'fail' => [],
        ];

        foreach ($rolesData as $role) {
            if ($this->deleteRole($role)) {
                $response['success'][] = $role->id;
            } else {
                $response['fail'][] = $role->id;
            }
        }

        return $this->success($response);
    }

    /**
     * 删除角色(只能用于不包含成员的角色).
     *
     * @param AdminRole $role
     *
     * @return bool
     */
    protected function deleteRole(AdminRole $role)
    {
        // 查询当前角色所包含的成员数量
        $roleUserCount = $role->adminUsers()->count();

        if (!$roleUserCount) {
            DB::beginTransaction();

            // 删除角色信息
            if ($role->delete()) {
                // 删除角色权限信息
                $role->adminRoleActions()->delete();

                DB::commit();

                return true;
            } else {
                DB::rollBack();
            }
        }

        return false;
    }
}
