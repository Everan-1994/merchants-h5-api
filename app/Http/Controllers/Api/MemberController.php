<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ApiResources\ActivityResource;
use App\Http\Resources\ApiResources\MyActivityReportResource;
use App\Http\Resources\ApiResources\MyTryUseReportResource;
use App\Http\Resources\ApiResources\TryUseResource;
use App\Models\ActivitySignUp;
use App\Models\Others;
use App\Models\Suggest;
use App\Models\User;
use App\Models\UseSignUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller;

class MemberController extends Controller
{
    /**
     * 关于我们
     * @return \Illuminate\Http\JsonResponse
     */
    public function aboutUs()
    {
        $about_us = Others::query()->select(['body'])->find(2);

        return response()->json($about_us);
    }

    /**
     * 提交建议信息
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function submitSuggest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required',
            'user_tel'  => 'required',
            'message'   => 'required',
        ], [
            'user_name.required' => '请填写姓名',
            'user_tel.required'  => '请填写手机号',
            'message.required'   => '请填写联建议内容',
        ]);

        if ($validator->fails()) {
            return response([
                'errorCode' => 1,
                'message'   => '建议信息有误',
                'errors'    => $validator->errors(),
            ]);
        }

        $builder = Suggest::query();

        $user_id = Auth::guard('user')->user()->id;

        $suggest_info = [
            'user_id'   => $user_id,
            'user_name' => $request->input('user_name'),
            'user_tel'  => $request->input('user_tel'),
            'message'   => $request->input('message'),
        ];

        try {
            // 纪录建议信息
            $builder->create($suggest_info);

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

    /**
     * 更新个人信息
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function updateUserInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar'    => 'required',
            'user_name' => 'required',
        ], [
            'avatar.required'    => '请上传头像',
            'user_name.required' => '请填写昵称',
        ]);

        if ($validator->fails()) {
            return response([
                'errorCode' => 1,
                'message'   => '信息有误',
                'errors'    => $validator->errors(),
            ]);
        }

        try {
            // 更新个人信息
            User::query()->where('id', Auth::guard('user')->user()->id)->update([
                'avatar' => $request->input('avatar'),
                'name'   => $request->input('user_name'),
            ]);

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

    /**
     * 我的报告
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function myReport(Request $request)
    {
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);

        $activity = self::joinList(1, $page, $page_size); // 活动
        $try_use = self::joinList(2, $page, $page_size); // 试用

        return response([
            'try_use'  => [
                'data'  => MyTryUseReportResource::collection($try_use),
                'total' => $try_use->total(),
            ],
            'activity' => [
                'data'  => MyActivityReportResource::collection($activity),
                'total' => $activity->total(),
            ],
        ]);
    }

    protected function joinList($type, $page, $page_size)
    {
        if ($type == 1) {
            $builder = ActivitySignUp::query()->with(['activity', 'report']); // 参与的活动
        } else {
            $builder = UseSignUp::query()->with(['try_use', 'report']); // 参与的试用
        }

        return $builder->where([
            'user_id' => Auth::guard('user')->user()->id,
            'status'  => 1,
        ])
            ->orderBy('created_at', 'desc')
            ->paginate($page_size, ['*'], 'page', $page);
    }

    /**
     * 申请参与的活动
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function myActivity(Request $request)
    {
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);

        $activity = ActivitySignUp::query()
            ->where('user_id', Auth::guard('user')->user()->id)
            ->with('activity')
            ->orderBy('created_at', 'desc')
            ->paginate($page_size, ['*'], 'page', $page); // 所有活动申请

        // 获取申请的活动
        $activity_arr = optional($activity)->map(function ($item, $key) {
            $item['activity']['apply_status'] = $item['status']; // 申请状态
            return $item['activity']; // 具体活动
        });

        return response([
            'data'  => ActivityResource::collection($activity_arr),
            'total' => $activity->total(),
        ]);
    }

    /**
     * 我的试用
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function myTryUse(Request $request)
    {
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);

        $try_use = UseSignUp::query()
            ->where('user_id', Auth::guard('user')->user()->id)
            ->with('try_use')
            ->orderBy('created_at', 'desc')
            ->paginate($page_size, ['*'], 'page', $page); // 所有活动申请

        // 获取申请的试用
        if ($try_use->isNotEmpty()) {
            $try_use_arr = optional($try_use)->map(function ($item, $key) {
                $item['try_use']['apply_status'] = $item['status']; // 申请状态
                return $item['try_use']; // 具体活动
            });
        } else {
            $try_use_arr = [];
        }

        return response([
            'data'  => TryUseResource::collection($try_use_arr),
            'total' => $try_use->total(),
        ]);
    }
}