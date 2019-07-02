<?php

namespace App\Http\Controllers;

use App\Http\Resources\Admin\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * 用户列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = User::query()
            ->when($request->input('startTime') && $request->input('endTime'), function ($query) use ($request) {
                $query->whereBetween('createdAt', [
                    date('Y-m-d H:i:s', $request->input('startTime')),
                    date('Y-m-d H:i:s', $request->input('endTime')),
                ]);
            })
            ->when($request->filled('name'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('name') . '%');
            })
            ->orderBy($request->input('order') ?: 'createdAt', $request->input('sort') ?: 'desc')
            ->paginate($request->input('pageSize', 10), ['*'], 'page', $request->input('page', 1));

        return $this->success([
            'data'  => UserResource::collection($user),
            'total' => $user->total(),
        ]);
    }

    /**
     * 状态更新
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateStatus($id, Request $request)
    {
        $rules = [
            'status'  => 'required|int',
        ];

        // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
        $params = $this->validate($request, $rules);

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int',
        ]);

        $winning = User::query()->whereId($id)->update($params);

        if ($winning) {
            return $this->success($winning, '更新成功');
        }

        return $this->fail(400, '更新失败');
    }
}