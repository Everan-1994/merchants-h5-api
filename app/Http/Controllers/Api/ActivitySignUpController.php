<?php

namespace App\Http\Controllers\Api;

use App\Models\ActivitySignUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller;

class ActivitySignUpController extends Controller
{
    /**
     * 活动申请报名
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'activity_id'    => 'required|numeric',
            'contact_name'   => 'required',
            'contact_phone'  => 'required',
            'sign_up_reason' => 'required',
        ], [
            'activity_id.required'    => '活动id不能为空',
            'activity_id.numeric'     => '活动id不能为字符串',
            'contact_name.required'   => '请填写联系人',
            'contact_phone.required'  => '请填写联系号码',
            'sign_up_reason.required' => '请填写申请理由',
        ]);

        if ($validator->fails()) {
            return response([
                'errorCode'    => 1,
                'message' => '报名信息有误',
                'errors'  => $validator->errors(),
            ]);
        }

        $builder = ActivitySignUp::query();

        $user_id = Auth::guard('user')->user()->id;
        $activity_id = $request->input('activity_id');

        // 查询是否已经存在报名信息
        if ($builder->where([
            ['user_id', '=', $user_id],
            ['activity_id', '=', $activity_id],
        ])->exists()) {
            return response([
                'errorCode'    => 1,
                'message' => '该活动已报名',
            ]);
        }

        $sign_up_info = [
            'user_id'        => $user_id,
            'activity_id'    => $activity_id,
            'contact_name'   => $request->input('contact_name'),
            'contact_phone'  => $request->input('contact_phone'),
            'sign_up_reason' => $request->input('sign_up_reason'),
        ];

        try {
            // 纪录申请信息
            $builder->create($sign_up_info);

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