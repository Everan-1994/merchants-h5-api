<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ApiResources\ExperienceReportResource;
use App\Models\ExperienceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller;

class ExperienceReportController extends Controller
{
    /**
     * 获取报告
     * @param $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function getReport($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response([
                'code'    => 1,
                'message' => '参数缺失',
                'errors'  => $validator->errors(),
            ]);
        }

        $builder = ExperienceReport::query();

        if (!$builder->where('id', $id)->exists()) {
            return response([
                'code'    => 1,
                'message' => '没有找到对应的报告',
            ]);
        }

        if ($builder->where('id', $id)->value('type') == 1) {
            $report_info = $builder->where('id', $id)
                ->with(['user', 'activity_report'])// 活动
                ->find($id);
        } else {
            $report_info = $builder->where('id', $id)
                ->with(['user', 'try_use_report'])// 试用
                ->find($id);
        }

        return response(new ExperienceReportResource($report_info));
    }

    public function addReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'    => 'required|numeric',
            'type_id' => 'required|numeric',
            'images'  => 'nullable',
            'content' => 'required',
        ], [
            'type.required'    => '类型id不能为空',
            'type.numeric'     => '类型id不能为字符串',
            'type_id.required' => '种类id不能为空',
            'type_id.numeric'  => '种类id不能为字符串',
            'content.required' => '请填内容',
        ]);

        if ($validator->fails()) {
            return response([
                'code'    => 1,
                'message' => '信息有误',
                'errors'  => $validator->errors(),
            ]);
        }

        $report_info = [
            'user_id' => Auth::guard('user')->user()->id,
            'type'    => $request->input('type'),
            'type_id' => $request->input('type_id'),
        ];

        try {
            // 心得
            ExperienceReport::query()->updateOrCreate($report_info, [
                'content' => $request->input('content'),
                'images'  => $request->exists('images') ? json_encode($request->input('images')) : '',
            ]);

            return response([
                'code'    => 0,
                'message' => 'success',
            ]);
        } catch (\Exception $exception) {
            return response([
                'code'    => $exception->getCode(),
                'message' => '服务器错误',
                'error'   => $exception->getMessage(),
            ]);
        }
    }

    /**
     * 图片上传
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function uploads(Request $request)
    {
        $images_url = []; // 图片地址

        if ($request->exists('images')) {
            foreach ($request->file('images') as $k => $file) {
                $images_url[] = 'http://' . env('QINIU_DOMAIN') . '/' . Storage::disk('qiniu')->put('apply', $file);
            }
        }

        return response($images_url);
    }


}