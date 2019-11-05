<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ApiResources\VideoResource;
use App\Models\Block;
use App\Models\BlockItem;
use App\Models\Watch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller;

class VideoController extends Controller
{
    /**
     * 视频模块列表
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);

        $blocks = Block::query()
            ->where('status', 1)
            ->orderBy('sort', 'desc')
            ->paginate($page_size, ['*'], 'page', $page);

        return response([
            'data'  => VideoResource::collection($blocks),
            'total' => $blocks->total(),
        ]);
    }

    public function getVideoList($id, Request $request)
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

        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);

        $builder = BlockItem::query();

        if (!$builder->where('blockId', $id)->exists()) {
            return response([
                'errorCode'    => 1,
                'message' => '没有找到对应的视频列表',
            ]);
        }

        $video_list = $builder->where([
            ['blockId', '=', $id],
            ['status', '=', 1],
        ])
            ->select(['id', 'title', 'watch_times', 'video', 'front_cover'])
            ->orderBy('sort', 'desc')
            ->paginate($page_size, ['*'], 'page', $page);

        return response([
            'data'  => optional($video_list)->toArray()['data'] ?: [],
            'total' => optional($video_list)->total() ?: 0,
        ]);
    }

    /*
     * 观看计数
     */
    public function watch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_id'    => 'required|int',
        ], [
            'video_id.required'    => '视频id不能为空',
        ]);

        if ($validator->fails()) {
            return response([
                'errorCode'    => 1,
                'message' => '信息有误',
                'errors'  => $validator->errors(),
            ]);
        }

        $builder = Watch::query();

        $data = [
            'user_id' => optional(Auth::guard('user')->user())->id ?: 0,
            'video_id' => $request->input('video_id'),
            'ip' => $request->getClientIp() // 用户ip
        ];

        $now = Carbon::now();

        if ($builder->where($data)
            ->whereBetween('created_at', [
                $now->copy()->toDateString() . ' 00:00:00',
                $now->copy()->toDateString() . ' 23:59:59',
            ])
            ->exists()) {
            return response([
                'errorCode'    => 1,
                'message' => '已计数'
            ]);
        }

        try {
            $builder->create($data);
            return response([
                'errorCode'    => 0,
                'message' => 'success'
            ]);
        } catch (\Exception $exception) {
            return response([
                'errorCode'    => 1,
                'message' => $exception->getMessage()
            ]);
        }
    }
}