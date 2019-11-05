<?php

namespace App\Http\Controllers;

use App\Http\Resources\Admin\PrizeResource;
use App\Models\Prize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrizeController extends Controller
{
    /**
     * 奖品列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $prizes = Prize::query()->get();

        return $this->success(PrizeResource::collection($prizes));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $rules = [
            'prize_name'  => 'required|string',
            'prize_num'   => 'required|int',
            'prize_image' => 'required|string',
            'probability' => 'required|int',
            'status'      => 'nullable|int',
        ];

        $params = $this->validate($request, $rules, [
            'probability.int' => '必须为整数'
        ], [
            'probability' => '中奖率'
        ]);

        if (!$this->totalProbability(0, $params['probability'])) {
            return $this->fail(400, '奖品列表的中奖率总和不能超过100%');
        }

        $result = Prize::query()->create($params);

        if ($result) {
            return $this->success($result, '添加成功');
        }

        return $this->fail(400, '添加失败');
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
            'prize_name'  => 'required|string',
            'prize_num'   => 'required|int',
            'prize_image' => 'required|string',
            'probability' => 'required|int',
            'status'      => 'nullable|int',
        ];

        // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
        $params = $this->validate($request, $rules, [
            'probability.int' => '必须为整数'
        ], [
            'probability' => '中奖率'
        ]);

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int',
        ]);

        if (!$this->totalProbability($id, $params['probability'])) {
            return $this->fail(400, '奖品列表的中奖率总和不能超过100%');
        }

        $prize = Prize::query()->whereId($id)->update($params);

        if ($prize) {
            return $this->success($prize, '编辑成功');
        }

        return $this->fail('编辑失败');
    }

    /**
     * 详情.
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

        $prize = Prize::query()->find($id);

        if ($prize) {
            return $this->success($prize);
        }

        return $this->fail(400, '获取详情失败');
    }

    /**
     * @param $id
     * @param $probability
     * @return bool
     */
    private function totalProbability($id, $probability)
    {
        if ($id > 0) {
            $total_probability = Prize::query()->where('id', '!=', $id)->sum('probability');
        } else {
            $total_probability = Prize::query()->sum('probability');
        }

        $total = $total_probability + $probability;

        return $total > 100 ? false : true;

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
        $numDestroied = Prize::query()
            ->whereIn('id', collect($params['ids']))
            ->delete();

        if ($num == $numDestroied) {
            return $this->success([], '删除成功');
        }

        return $this->fail(400, '删除失败');
    }

}