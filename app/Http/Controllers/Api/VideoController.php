<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ApiResources\VideoResource;
use App\Models\Block;
use App\Models\BlockItem;
use Illuminate\Http\Request;
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
                'code'    => 1,
                'message' => '参数缺失',
                'errors'  => $validator->errors(),
            ]);
        }

        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);

        $builder = BlockItem::query();

        if (!$builder->where('blockId', $id)->exists()) {
            return response([
                'code'    => 1,
                'message' => '没有找到对应的视频列表',
            ]);
        }

        $video_list = $builder->where([
            ['blockId', '=', $id],
            ['status', '=', 1],
        ])
            ->select(['id', 'title', 'watch_times', 'video'])
            ->orderBy('sort', 'desc')
            ->paginate($page_size, ['*'], 'page', $page);

        return response([
            'data'  => optional($video_list)->toArray()['data'] ?: [],
            'total' => optional($video_list)->total() ?: 0,
        ]);
    }
}