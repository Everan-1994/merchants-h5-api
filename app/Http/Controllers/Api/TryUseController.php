<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ApiResources\TryUseResource;
use App\Models\TryUse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller;

class TryUseController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);

        $try_use = TryUse::query()
            ->where('status', 1)
            ->where('apply_start', '<', Carbon::now())
            ->with('signs')
            ->orderBy('sort', 'desc')
            ->paginate($page_size, ['*'], 'page', $page);

        return response([
            'data' => TryUseResource::collection($try_use),
            'total' => $try_use->total()
        ]);
    }

    /**
     * 试用详情
     * @param $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function getTryUseDetail($id)
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

        $builder = TryUse::query();

        if (!$builder->where('id', $id)->exists()) {
            return response([
                'errorCode'    => 1,
                'message' => '没有找到对应的试用商品',
            ]);
        }

        $try_use_info = $builder->where('status', 1)->with(['sign', 'reports'])->find($id);

        return response(new TryUseResource($try_use_info));
    }
}