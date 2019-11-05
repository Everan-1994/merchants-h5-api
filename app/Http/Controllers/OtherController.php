<?php

namespace App\Http\Controllers;

use App\Models\Others;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OtherController extends Controller
{

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
            'body'  => 'nullable|string',
            'content'  => 'nullable|string',
        ];

        // 验证参数，如果验证失败，则会抛出 ValidationException 的异常
        $params = $this->validate($request, $rules);

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int',
        ]);

        $prize = Others::query()->whereId($id)->update($params);

        if ($prize) {
            return $this->success($prize, '编辑成功');
        }

        return $this->fail(400, '编辑失败');
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

        $other = Others::query()->find($id);

        if ($other) {
            return $this->success($other);
        }

        return $this->fail(400, '获取详情失败');
    }

}