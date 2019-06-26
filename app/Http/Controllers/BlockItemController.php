<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\BlockItem;
use App\Traits\UpdateSort;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BlockItemController extends Controller
{
    use UpdateSort;

    /**
     * @param Request $request
     * @param $blockId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function readAll(Request $request, $blockId)
    {
        $validator = Validator::make(['blockId' => $blockId], [
            'blockId' => 'required|int',
        ]);

        try {
            Block::query()->findOrFail($blockId);
        } catch (ModelNotFoundException $e) {
            $this->throwExeptionByCode('视频模块不存在');
        }

        $items = BlockItem::query()->select('id', 'blockId', 'title', 'watch_times', 'front_cover', 'video', 'status', 'sort', 'createdAt')
            ->where('blockId', '=', $blockId)
            ->when($request->exists('title'), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->input('title') . '%');
            })
            ->when($request->input('startTime') && $request->input('endTime'), function ($query) use ($request) {
                return $query->whereBetween('createdAt', [
                    date('Y-m-d H:i:s', $request->input('startTime')),
                    date('Y-m-d ' . '23:59:59', $request->input('endTime')),
                ]);
            })
            ->orderBy('sort', 'desc')
            ->paginate($request->input('pageSize') ?: 10, ['*'], 'page', $request->input('page') ?: 1);

        return $this->success([
            'data' => optional($items)->toArray()['data'] ?: [],
            'meta' => [
                'total' => $items->total(),
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function read(Request $request, $id = null)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int',
        ]);
        $item = BlockItem::query()->select('id', 'blockId', 'title', 'watch_times', 'front_cover', 'video', 'status', 'sort', 'createdAt')
            ->whereId($id)
            ->first();
        if ($item) {
            return $this->success($item->toArray());
        } else {
            $this->throwExeptionByCode('视频列表不存在');
        }
    }

    /**
     * @param Request $request
     * @param null    $id
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createOrUpdate(Request $request, $id = null)
    {
        $params = $this->validate($request, [
            'blockId' => 'required|int',
            'title' => 'string|max:255',
            'front_cover' => 'string',
            'video' => 'string',
            'sort' => 'int',
        ]);
        if (!empty($id)) {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|int',
            ]);
            $params['id'] = $id;
        }
        try {
            Block::query()->findOrFail($params['blockId']);
        } catch (ModelNotFoundException $e) {
            $this->throwExeptionByCode('视频模块不存在');
        }

        try {
            if (isset($params['id']) && $params['id']) {
                $blockItem = BlockItem::query()->findOrFail($params['id']);
            } else {
                $blockItem = new BlockItem();
                $blockItem->sort = time();
            }
        } catch (ModelNotFoundException $e) {
            $this->throwExeptionByCode('视频列表不存在');
        }
        $blockItem->fill($params);
        $result = $blockItem->save();
        if ($result) {
            return $this->success(['id' => $blockItem->id]);
        } else {
            $this->throwExeptionByCode('添加或更新失败');
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Request $request)
    {
        $params = $this->validate($request, [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|distinct|int',
        ]);

        $num = count($params['ids']);
        DB::beginTransaction();
        $numDestroied = BlockItem::destroy($params['ids']);
        if ($num == $numDestroied) {
            DB::commit();

            return $this->success();
        } else {
            DB::rollBack();
            $this->throwExeptionByCode('视频删除失败');
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sort(Request $request)
    {
        $params = $this->validate($request, [
            'item' => 'required|array|min:1',
            'sortType' => 'required|string',
        ]);

        if ($this->commonSort(
            app(BlockItem::class),
            $params['sortType'],
            $params['item']['id'],
            $params['item']['sort'],
            'blockId',
            $params['item']['parentId']
        )) {
            return $this->success();
        }

        return $this->fail('排序失败');
    }
}
