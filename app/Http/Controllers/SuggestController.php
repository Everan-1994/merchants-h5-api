<?php

namespace App\Http\Controllers;

use App\Http\Resources\Admin\SuggestResource;
use App\Models\Suggest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuggestController extends Controller
{
    /**
     * 反馈列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $suggest = Suggest::query()
            ->when($request->input('startTime') && $request->input('endTime'), function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    date('Y-m-d H:i:s', $request->input('startTime')),
                    date('Y-m-d H:i:s', $request->input('endTime')),
                ]);
            })
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $query->where('user_name', 'like', '%' . $request->input('keyword') . '%')
                    ->orWhere('user_tel', 'like', '%' . $request->input('keyword') . '%');
            })
            ->orderBy($request->input('order') ?: 'created_at', $request->input('sort') ?: 'desc')
            ->paginate($request->input('pageSize', 10), ['*'], 'page', $request->input('page', 1));

        return $this->success([
            'data'  => SuggestResource::collection($suggest),
            'total' => $suggest->total(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Request $request)
    {
        $params = $this->validate($request, [
            'ids'   => 'required|array|min:1',
            'ids.*' => 'required|distinct|int',
        ]);

        $num = count($params['ids']);
        $numDestroied = Suggest::query()
            ->whereIn('id', collect($params['ids']))
            ->delete();

        if ($num == $numDestroied) {
            return $this->success([], '删除成功');
        }

        return $this->fail(400, '删除失败');
    }
}