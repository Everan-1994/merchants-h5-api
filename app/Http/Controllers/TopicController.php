<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Traits\UpdateSort;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TopicController extends Controller
{
    use UpdateSort;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $topic = Topic::query()
            ->select(['id', 'title', 'created_at', 'sort'])
            ->when($request->input('startTime') && $request->input('endTime'), function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    date('Y-m-d H:i:s', $request->input('startTime')),
                    date('Y-m-d ' . '23:59:59', $request->input('endTime')),
                ]);
            })
            ->when($request->filled('title'), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->input('title') . '%');
            })
            ->orderBy($request->input('order') ?: 'created_at', $request->input('sort') ?: 'desc')
            ->paginate($request->input('pageSize', 10), ['*'], 'page', $request->input('page', 1));

        return $this->success([
            'data'  => optional($topic)->toArray()['data'] ?: [],
            'total' => $topic->total(),
        ]);
    }

    /**
     * 话题详情.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric',
        ]);

        $topic = Topic::query()->find($id);

        if ($topic) {
            return $this->success($topic);
        }

        return $this->fail('获取详情失败');
    }

    /**
     * 新增
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $rules = [
            'title'       => 'required|string',
            'front_cover' => 'nullable|string',
            'content'     => 'required|string',
        ];

        // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
        $params = $this->validate($request, $rules);

        $params['userId'] = Auth::id();
        $params['sort'] = Carbon::now()->timestamp;

        $result = Topic::query()->create($params);

        if ($result) {
            return $this->success($result, '添加成功');
        }

        return $this->fail('添加失败');
    }

    /**
     * 编辑.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update($id, Request $request)
    {
        $rules = [
            'title'       => 'required|string',
            'front_cover' => 'nullable|string',
            'content'     => 'required|string',
        ];

        // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
        $params = $this->validate($request, $rules);

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int',
        ]);

        $topic = Topic::query()->whereId($id)->update($params);

        if ($topic) {
            return $this->success($topic, '编辑成功');
        }

        return $this->fail('编辑失败');
    }

    /**
     * 删除.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Request $request)
    {
        $params = $this->validate($request, [
            'ids'   => 'required|array|min:1',
            'ids.*' => 'required|distinct|int',
        ]);

        $num = count($params['ids']);
        $numDestroied = Topic::query()
            ->whereIn('id', collect($params['ids']))
            ->delete();

        if ($num == $numDestroied) {
            return $this->success([], '删除成功');
        }

        return $this->fail('删除失败');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateSort(Request $request)
    {
        $rules = [
            'sortType' => 'required|string',
            'item' => 'required|array|min:1',
        ];

        $params = $this->validate($request, $rules);

        if ($this->commonSort(
            Topic::class,
            $params['sortType'],
            $params['item']['id'],
            $params['item']['sort'],
            '',
            '',
            'sort',
            '',
            $request
        )) {
            return $this->success();
        }

        return $this->fail('排序失败');
    }
}