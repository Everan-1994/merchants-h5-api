<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ApiResources\ActivityResource;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);

        $activity = Activity::query()
            ->where('status', 1)
            ->where('apply_start', '<', Carbon::now())
            ->orderBy('sort', 'desc')
            ->paginate($page_size, ['*'], 'page', $page);

        return response([
            'data' => ActivityResource::collection($activity),
            'total' => $activity->total()
        ]);
    }

    /**
     * 活动详情
     * @param $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function getActivityDetail($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response([
                'errorCode'    => 1,
                'message' => '参数缺失',
                'errors'  => $validator->errors(),
            ]);
        }

        $builder = Activity::query();

        if (!$builder->where('id', $id)->exists()) {
            return response([
                'errorCode'    => 1,
                'message' => '没有找到对应的活动',
            ]);
        }

        $activity_info = $builder->where('status', 1)
            ->with(['signs', 'reports'])
            ->find($id);

        return response(new ActivityResource($activity_info));
    }
}