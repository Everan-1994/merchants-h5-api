<?php

namespace App\Http\Controllers\Api;

use App\Models\UseSignUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller;

class UseSignUpController extends Controller
{
    /**
     * 试用申请报名
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'use_id'        => 'required|numeric',
            'contact_name'  => 'required',
            'contact_phone' => 'required',
            'province'      => 'required',
            'city'          => 'required',
            'district'      => 'required',
            'address'       => 'required'
        ], [
            'use_id.required'        => '活动id不能为空',
            'use_id.numeric'         => '活动id不能为字符串',
            'contact_name.required'  => '请填写联系人',
            'contact_phone.required' => '请填写联系号码',
            'province.required'      => '请选择省份',
            'city.required'          => '请选择市',
            'district.required'      => '请选择区',
            'address.required'       => '请填写详细收件地址'
        ]);

        if ($validator->fails()) {
            return response([
                'errorCode'    => 1,
                'message' => '报名信息有误',
                'errors'  => $validator->errors(),
            ]);
        }

        $builder = UseSignUp::query();

        $user_id = Auth::guard('user')->user()->id;
        $use_id = $request->input('use_id');

        // 查询是否已经存在报名信息
        if ($builder->where([
            ['user_id', '=', $user_id],
            ['use_id', '=', $use_id],
        ])->exists()) {
            return response([
                'errorCode'    => 1,
                'message' => '已经参与过申请',
            ]);
        }

        $sign_up_info = [
            'user_id'       => $user_id,
            'use_id'        => $use_id,
            'contact_name'  => $request->input('contact_name'),
            'contact_phone' => $request->input('contact_phone'),
            'province'      => $request->input('province'),
            'city'          => $request->input('city'),
            'district'      => $request->input('district'),
            'address'       => $request->input('address'),
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