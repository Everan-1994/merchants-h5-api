<?php

namespace App\Http\Controllers;

use App\Http\Resources\Admin\TryUseResource;
use App\Models\TryUse;
use App\Models\UseSignUp;
use App\Traits\UpdateSort;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TryUseController extends Controller
{
    use UpdateSort;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $try_use = TryUse::query()
            ->when($request->input('startTime') && $request->input('endTime'), function ($query) use ($request) {
                $query->where('apply_start', '<=', date('Y-m-d H:i:s', $request->input('startTime')))
                    ->where('apply_end', '>=', date('Y-m-d H:i:s', $request->input('startTime')));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                switch ($request->input('status')){
                    case 0: // 未开始
                        $query->where('apply_start', '>', Carbon::now()->toDateTimeString());
                        break;
                    case 1: // 进行中
                        $query->where('apply_start', '<=', Carbon::now()->toDateTimeString())
                            ->where('apply_end', '>=', Carbon::now()->toDateTimeString());
                        break;
                    case 2: // 已结束
                        $query->where('apply_end', '<=', Carbon::now()->toDateTimeString());
                        break;
                }
            })
            ->when($request->filled('name'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->input('name') . '%');
            })
            ->orderBy($request->input('order', 'sort'), $request->input('sort', 'desc'))
            ->paginate($request->input('pageSize', 10), ['*'], 'page', $request->input('page', 1));

        return $this->success([
            'data'  => TryUseResource::collection($try_use),
            'total' => $try_use->total(),
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

        $try_use = TryUse::query()->find($id);

        if ($try_use) {
            return $this->success([
                'name'          => $try_use->name,
                'front_cover'   => $try_use->front_cover,
                'stock'         => $try_use->stock,
                'price'         => $try_use->price,
                'apply_start'   => $try_use->apply_start,
                'apply_end'     => $try_use->apply_end,
                'product_intro' => implode(',', json_decode($try_use->product_intro, true)),
                'default_list'  => json_decode($try_use->product_intro, true),
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
            'name'          => 'required|string',
            'front_cover'   => 'required|string',
            'stock'         => 'required|int',
            'price'         => 'required',
            'apply_start'   => 'required',
            'apply_end'     => 'required',
            'product_intro' => 'required',
        ];

        // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
        $params = $this->validate($request, $rules);

        $params['sort'] = Carbon::now()->timestamp;
        $params['product_intro'] = json_encode(explode(',', $params['product_intro']));
        $params['apply_start'] = date('Y-m-d H:i:s', $params['apply_start']);
        $params['apply_end'] = date('Y-m-d H:i:s', $params['apply_end']);

        $result = TryUse::query()->create($params);

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
            'name'          => 'required|string',
            'front_cover'   => 'required|string',
            'stock'         => 'required|int',
            'price'         => 'required',
            'apply_start'   => 'required',
            'apply_end'     => 'required',
            'product_intro' => 'required',
        ];

        // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
        $params = $this->validate($request, $rules);

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int',
        ]);

        $params['product_intro'] = json_encode(explode(',', $params['product_intro']));
        $params['apply_start'] = date('Y-m-d H:i:s', $params['apply_start']);
        $params['apply_end'] = date('Y-m-d H:i:s', $params['apply_end']);

        $try_use = TryUse::query()->whereId($id)->update($params);

        if ($try_use) {
            return $this->success($try_use, '编辑成功');
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
            $numDestroied = TryUse::query()
                ->whereIn('id', collect($params['ids']))
                ->delete();

            UseSignUp::query()->whereIn('use_id', $params['ids'])->delete();

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
            TryUse::class,
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