<?php

namespace App\Http\Controllers\Api;

use App\Models\Winning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller;

class WinningController extends Controller
{
    /**
     * 纪录中奖信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prize_name'   => 'required',
            'prize_img'    => 'required',
            'prize_status' => 'required',
        ], [
            'prize_name.required'   => '奖品名称不能为空',
            'prize_img.required'    => '奖品图片不能为空',
            'prize_status.required' => '奖品状态不能为空',
        ]);

        if ($validator->fails()) {
            return response([
                'errorCode' => 1,
                'message'   => '中奖信息有误',
                'errors'    => $validator->errors(),
            ]);
        }

        $winning_info = [
            'user_id'       => Auth::guard('user')->user()->id,
            'prize_name'    => $request->input('prize_name'),
            'prize_img'     => $request->input('prize_img'),
            'contact_name'  => $request->input('contact_name', ''),
            'contact_phone' => $request->input('contact_phone', ''),
            'province'      => $request->input('province', ''),
            'city'          => $request->input('city', ''),
            'district'      => $request->input('district', ''),
            'address'       => $request->input('address', ''),
            'prize_status'  => $request->input('prize_status'),
        ];

        try {
            // 纪录中奖信息
            Winning::query()->create($winning_info);

            return response([
                'errorCode' => 0,
                'message'   => 'success',
            ]);
        } catch (\Exception $exception) {
            return response([
                'errorCode' => $exception->getCode(),
                'message'   => '服务器错误',
                'error'     => $exception->getMessage(),
            ]);
        }
    }

    /*
     * 获取中奖信息 by id
     */
    public function getWinningInfo($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response([
                'errorCode' => 1,
                'message'   => '参数缺失',
                'errors'    => $validator->errors(),
            ]);
        }

        $builder = Winning::query();

        if (!$builder->where('id', $id)->exists()) {
            return response([
                'errorCode' => 1,
                'message'   => '没有对应中奖信息',
            ]);
        }

        $winning_info = $builder->select([
            'prize_name',
            'prize_img',
            'prize_status',
            'contact_name',
            'contact_phone',
            'province',
            'city',
            'district',
            'address',
        ])->find($id);

        return response($winning_info);
    }

    /**
     * 获取中奖信息 by date
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function getWinningInfoByDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d',
        ], [
            'date.required'    => '中奖日期不能为空',
            'date.date_format' => '日期格式不正确',
        ]);

        if ($validator->fails()) {
            return response([
                'errorCode' => 1,
                'message'   => '参数缺失',
                'errors'    => $validator->errors(),
            ]);
        }

        $builder = Winning::query();

        if (!$builder->where('user_id', Auth::guard('user')->user()->id)
            ->whereBetween('created_at', [
                $request->input('date') . ' 00:00:00',
                $request->input('date') . ' 23:59:59',
            ])
            ->exists()) {
            return response([
                'errorCode' => 1,
                'message'   => '没有对应中奖信息',
            ]);
        }

        $winning_info = $builder->select([
            'prize_name',
            'prize_img',
            'prize_status',
            'contact_name',
            'contact_phone',
            'province',
            'city',
            'district',
            'address',
        ])->first();

        return response($winning_info);
    }
}