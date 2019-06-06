<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminUserResource;
use App\Models\AdminRole;
use App\Models\AdminRoleUser;
use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MemberController extends Controller
{
    /**
     * 获取成员列表.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function lists()
    {
        $members = AdminUser::all();
        $data = [];
        foreach ($members as $member) {
            /* @var AdminUser $member */
            $data[] = [
                'id' => $member->id,
                'username' => $member->username,
                'email' => $member->email,
                'realname' => $member->realname,
                'role' => $member->adminRoles()->value('name'),
                'isEnable' => $member->isEnable,
                'createdAt' => $member->createdAt->toDateTimeString(),
            ];
        }

        return $this->success($data);
    }

    /**
     * 成员详情.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws ValidationException
     */
    public function detail($id)
    {
        $memberData = AdminUser::query()->find($id);
        if (!$memberData) {
            return $this->fail(UNKNOWN_MEMBER);
        }

        /** @var AdminUser $memberData */
        $response = [
            'id' => $memberData->id,
            'username' => $memberData->username,
            'email' => $memberData->email,
            'realname' => $memberData->realname,
            'role' => $memberData->adminRoles()->value('id'),
            'isEnable' => $memberData->isEnable,
        ];

        return $this->success($response);
    }

    /**
     * 添加成员.
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
            'username' => 'required|uniqueSoftDelete:admin_users,username',
            'email' => 'required|uniqueSoftDelete:admin_users,email',
            'realname' => 'required|uniqueSoftDelete:admin_users,realname',
            'roleId' => 'required|int',
            'password' => 'required|string|between:5,20',
            'isEnable' => 'required|int',
        ]);

        $currentUser = $request->user();
        $superRoleData = AdminRole::query()->where('isSuper', 1)->first()->toArray();

        if ($superRoleData['id'] == $params['roleId'] && !$this->isSuper($currentUser['id'])) {
            return $this->fail(PERMISSION_DENIED);
        }

        // 保存成员信息
        $userModel = new AdminUser();
        $userModel->username = $params['username'];
        $userModel->email = $params['email'];
        $userModel->realname = $params['realname'];
        $userModel->password = app('hash')->make($params['password']);
        $userModel->isEnable = $params['isEnable'];

        // 开启事务
        DB::beginTransaction();

        if ($userModel->save()) {
            // 保存成员角色对应关系信息
            $roleUserModel = new AdminRoleUser();
            $roleUserModel->adminRoleId = $params['roleId'];
            $userModel->adminRoleUsers()->save($roleUserModel);

            DB::commit();

            return $this->success();
        } else {
            DB::rollBack();
        }

        return $this->fail(ADD_MEMBER_ERROR);
    }

    /**
     * 判断当前登录用户是否为超级管理员.
     *
     * @param int $userId
     *
     * @return bool
     */
    protected function isSuper($userId)
    {
        // 查询当前登录用户角色
        $currentUserRoleData = AdminUser::query()
            ->with('roles')
            ->where('id', $userId)
            ->first()
            ->toArray();

        // 只取第一个角色
        if (count($currentUserRoleData['roles']) > 0) {
            return $currentUserRoleData['roles'][0]['isSuper'] ? true : false;
        }

        return false;
    }

    /**
     * 更新成员信息.
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
            'username' => 'required|string',
            'email' => 'required|email',
            'realname' => 'required|string',
            'roleId' => 'required|int',
            'password' => 'present|string|between:5,20',
            'isEnable' => 'required',
        ]);

        $memberData = AdminUser::query()->find($params['id']);
        if (!$memberData) {
            return $this->fail(UNKNOWN_MEMBER);
        }

        if ($this->isSuper($memberData->id)) {
            $currentUser = $request->user();
            if (!$this->isSuper($currentUser['id']) || !$params['isEnable']) {
                return $this->fail(PERMISSION_DENIED);
            }
        }

        // 更新成员信息
        /* @var AdminUser $memberData */
        $memberData->username = $params['username'];
        $memberData->email = $params['email'];
        $memberData->realname = $params['realname'];
        if ($params['password']) {
            $memberData->password = app('hash')->make($params['password']);
        }
        $memberData->isEnable = $params['isEnable'];

        // 开启事务
        DB::beginTransaction();

        if ($memberData->update()) {
            // 更新成员角色管理信息
            if (false !== $memberData->adminRoleUsers()->update(['adminRoleId' => $params['roleId']])) {
                DB::commit();

                return $this->success();
            } else {
                DB::rollBack();
            }
        }

        return $this->fail(UPDATE_MEMBER_ERROR);
    }

    /**
     * 删除多个成员.
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

        /** @var Collection $usersData */
        $usersData = AdminUser::query()->findMany($params['ids'])->all();

        if (!$usersData) {
            return $this->fail(UNKNOWN_MEMBER);
        }

        // 初始返回数据
        $response = [
            'success' => [],
            'fail' => [],
        ];

        foreach ($usersData as $user) {
            /** @var AdminUser $user */
            if ($this->deleteMember($user)) {
                $response['success'][] = $user->id;
            } else {
                $response['fail'][] = $user->id;
            }
        }

        return $this->success($response);
    }

    /**
     * 删除成员(无法删除管理员).
     *
     * @param AdminUser $member
     *
     * @return bool
     */
    protected function deleteMember($member)
    {
        if ($this->isSuper($member->id)) {
            return false;
        }

        // 开启事务
        DB::beginTransaction();
        if ($member->delete()) {
            $member->adminRoleUsers()->delete();
            DB::commit();

            return true;
        } else {
            DB::rollBack();
        }

        return false;
    }

    /**
     * 成员启用状态修改.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws ValidationException
     */
    public function changeStatus(Request $request)
    {
        $params = $this->validate($request, [
            'id' => 'required|int',
            'isEnable' => 'required',
        ]);

        $memberData = AdminUser::query()->find($params['id']);
        /** @var AdminUser $memberData */
        if (!$memberData) {
            return $this->fail(UNKNOWN_MEMBER);
        }

        // 获取当前登录用户信息
        $userData = (new AdminUserResource(Auth::guard('api')->user()));

        if ($this->isSuper($memberData['id']) || $memberData['id'] == $userData['id']) {
            return $this->fail(PERMISSION_DENIED);
        }

        $result = AdminUser::query()->where('id', $params['id'])->update([
            'isEnable' => $params['isEnable'],
        ]);

        if ($result) {
            return $this->success();
        }

        return $this->fail(UPDATE_MEMBER_ERROR);
    }
}
