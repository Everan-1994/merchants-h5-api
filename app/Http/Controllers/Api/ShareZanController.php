<?php

namespace App\Http\Controllers\Api;

use App\Models\ActivitySignUp;
use App\Models\Share;
use App\Models\UseSignUp;
use App\Models\Zan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller;

class ShareZanController extends Controller
{
    /**
     * 分享接口
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function share(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'    => 'required',
            'type_id' => 'required',
        ], [
            'type.required'    => '类型id不能为空',
            'type_id.required' => '主题id不能为空',
        ]);

        if ($validator->fails()) {
            return response([
                'errorCode'    => 1,
                'message' => '信息有误',
                'errors'  => $validator->errors(),
            ]);
        }



        if ($request->input('type') == 1) {
            $builder = ActivitySignUp::query();
            $where = [
                'user_id' => Auth::guard('user')->user()->id,
                'activity_id' => $request->input('type_id')
            ];
        } else {
            $builder = UseSignUp::query();
            $where = [
                'user_id' => Auth::guard('user')->user()->id,
                'use_id' => $request->input('type_id')
            ];
        }

        if (!$builder->where($where)->exists()) {
            return response([
                'errorCode'    => 1,
                'message' => '该用户暂无此活动或试用的申请'
            ]);
        }

        try {

            $share_info = [
                'user_id' => Auth::guard('user')->user()->id,
                'type'    => $request->input('type'),
                'type_id' => $request->input('type_id'),
            ];

            // 纪录分享信息
            Share::query()->create($share_info);

            return response([
                'errorCode'    => 0,
                'message' => 'success',
            ]);
        } catch (\Exception $exception) {
            return response([
                'errorCode'    => $exception->getCode(),
                'message' => '服务器错误',
                'error'   => $exception->getMessage(),
            ]);
        }
    }

    public function zan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'    => 'required',
            'type_id' => 'required',
        ], [
            'type.required'    => '类型id不能为空',
            'type_id.required' => '主题id不能为空',
        ]);

        if ($validator->fails()) {
            return response([
                'errorCode'    => 1,
                'message' => '信息有误',
                'errors'  => $validator->errors(),
            ]);
        }

        $zan_info = [
            'user_id' => Auth::guard('user')->user()->id,
            'type'    => $request->input('type'),
            'type_id' => $request->input('type_id'),
        ];

        try {

            $builder = Zan::query();

            if ($builder->where($zan_info)->exists()) {
                // 取消点赞
                $zan = $builder->where($zan_info)->first();
                $zan->delete();
            } else {
                // 纪录点赞信息
                $builder->create($zan_info);
            }

            return response([
                'errorCode'    => 0,
                'message' => 'success',
            ]);
        } catch (\Exception $exception) {
            return response([
                'errorCode'    => $exception->getCode(),
                'message' => '服务器错误',
                'error'   => $exception->getMessage(),
            ]);
        }
    }
}