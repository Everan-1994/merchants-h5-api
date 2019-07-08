<?php

namespace App\Http\Controllers;

use App\Http\Resources\Admin\ActivityResource;
use App\Models\Activity;
use App\Models\ActivitySignUp;
use App\Traits\UpdateSort;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    use UpdateSort;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $activity = Activity::query()
            ->when($request->input('startTime') && $request->input('endTime'), function ($query) use ($request) {
                $query->where('activity_start', '<=', date('Y-m-d H:i:s', $request->input('startTime')))
                    ->where('activity_end', '>=', date('Y-m-d H:i:s', $request->input('startTime')));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                switch ($request->input('status')) {
                    case 0: // 未开始
                        $query->where('activity_start', '>', Carbon::now()->toDateTimeString());
                        break;
                    case 1: // 进行中
                        $query->where('activity_start', '<=', Carbon::now()->toDateTimeString())
                            ->where('apply_end', '>=', Carbon::now()->toDateTimeString());
                        break;
                    case 2: // 已结束
                        $query->where('activity_end', '<=', Carbon::now()->toDateTimeString());
                        break;
                }
            })
            ->when($request->filled('name'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('name') . '%');
            })
            ->orderBy($request->input('order', 'sort'), $request->input('sort', 'desc'))
            ->paginate($request->input('pageSize', 10), ['*'], 'page', $request->input('page', 1));

        return $this->success([
            'data'  => ActivityResource::collection($activity),
            'total' => $activity->total(),
        ]);
    }

    /**
     * 试用详情.
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

        $activity = Activity::query()->find($id);

        if ($activity) {
            return $this->success([
                'name'           => $activity->name,
                'front_cover'    => $activity->front_cover,
                'content'        => $activity->content,
                'address'        => $activity->address,
                'limit'          => $activity->limit,
                'apply_start'    => $activity->apply_start,
                'apply_end'      => $activity->apply_end,
                'activity_start' => $activity->activity_start,
                'activity_end'   => $activity->activity_end,
                'activity_intro' => implode(',', json_decode($activity->activity_intro, true)),
                'default_list'   => json_decode($activity->activity_intro, true),
            ]);
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
            'name'           => 'required|string',
            'front_cover'    => 'required|string',
            'limit'          => 'required|int',
            'activity_start' => 'required',
            'activity_end'   => 'required',
            'apply_start'    => 'required',
            'apply_end'      => 'required',
            'activity_intro' => 'required',
            'content'        => 'required',
            'address'        => 'required',
        ];

        // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
        $params = $this->validate($request, $rules);

        $params['sort'] = Carbon::now()->timestamp;
        $params['activity_intro'] = json_encode(explode(',', $params['activity_intro']));
        $params['apply_start'] = date('Y-m-d H:i:s', $params['apply_start']);
        $params['apply_end'] = date('Y-m-d H:i:s', $params['apply_end']);
        $params['activity_start'] = date('Y-m-d H:i:s', $params['activity_start']);
        $params['activity_end'] = date('Y-m-d H:i:s', $params['activity_end']);

        $result = Activity::query()->create($params);

        if ($result) {
            return $this->success($result, '添加成功');
        }

        return $this->fail('添加失败');
    }

    /**
     * 编辑
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update($id, Request $request)
    {
        $rules = [
            'name'           => 'required|string',
            'front_cover'    => 'required|string',
            'limit'          => 'required|int',
            'activity_start' => 'required',
            'activity_end'   => 'required',
            'apply_start'    => 'required',
            'apply_end'      => 'required',
            'activity_intro' => 'required',
            'content'        => 'required',
            'address'        => 'required',
        ];

        // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
        $params = $this->validate($request, $rules);

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int',
        ]);

        $params['activity_intro'] = json_encode(explode(',', $params['activity_intro']));
        $params['apply_start'] = date('Y-m-d H:i:s', $params['apply_start']);
        $params['apply_end'] = date('Y-m-d H:i:s', $params['apply_end']);
        $params['activity_start'] = date('Y-m-d H:i:s', $params['activity_start']);
        $params['activity_end'] = date('Y-m-d H:i:s', $params['activity_end']);

        $activity = Activity::query()->whereId($id)->update($params);

        if ($activity) {
            return $this->success($activity, '编辑成功');
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
        DB::beginTransaction();
        try {
            $numDestroied = Activity::query()
                ->whereIn('id', collect($params['ids']))
                ->delete();

            ActivitySignUp::query()->whereIn('activity_id', $params['ids'])->delete();

            if ($num == $numDestroied) {
                DB::commit();
                return $this->success([], '删除成功');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->fail('删除失败');
        }
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
            'item'     => 'required|array|min:1',
        ];

        $params = $this->validate($request, $rules);

        if ($this->commonSort(
            Activity::class,
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