<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\BlockItem;
use App\Traits\UpdateSort;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class BlockItemController extends Controller
{
    use UpdateSort;

    /**
     * Get all block items data.
     */
    public function readAll(Request $request, $blockId)
    {
        $validator = Validator::make(['blockId' => $blockId], [
            'blockId' => 'required|int',
        ]);

        try {
            Block::findOrFail($blockId);
        } catch (ModelNotFoundException $e) {
            $this->throwExeptionByCode(BLOCK_NOT_EXIST);
        }

        $items = BlockItem::select('id', 'blockId', 'title', 'description', 'url', 'imagePath', 'sort', 'createdAt', 'updatedAt')
            ->where('blockId', '=', $blockId)
            ->orderBy('sort', 'desc')
            ->get();

        return $this->success($items->toArray());
    }

    /**
     * Get the detail data of the block item.
     *
     * @param Request $request
     * @param int     $id      :     block item id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(Request $request, $id = null)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int',
        ]);
        $item = BlockItem::select('id', 'blockId', 'title', 'description', 'url', 'imagePath', 'sort', 'createdAt', 'updatedAt')
            ->whereId($id)
            ->first();
        if ($item) {
            return $this->success($item->toArray());
        } else {
            $this->throwExeptionByCode(BLOCK_ITEM_NOT_EXIST);
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
            'description' => 'string',
            'url' => 'string|max:255',
            'imagePath' => 'string|max:255',
            'sort' => 'int',
        ]);
        if (!empty($id)) {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|int',
            ]);
            $params['id'] = $id;
        }
        try {
            Block::findOrFail($params['blockId']);
        } catch (ModelNotFoundException $e) {
            $this->throwExeptionByCode(BLOCK_NOT_EXIST);
        }

        try {
            if (isset($params['id']) && $params['id']) {
                $blockItem = BlockItem::findOrFail($params['id']);
            } else {
                $blockItem = new BlockItem();
                $blockItem->sort = time();
            }
        } catch (ModelNotFoundException $e) {
            $this->throwExeptionByCode(BLOCK_ITEM_NOT_EXIST);
        }
        $blockItem->fill($params);
        $result = $blockItem->save();
        if ($result) {
            return $this->success(['id' => $blockItem->id]);
        } else {
            $this->throwExeptionByCode(BLOCK_ITEM_CREATE_OR_UPDATE_ERROR);
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
            $this->throwExeptionByCode(BLOCK_ITEM_DELETE_ERROR);
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

        return $this->fail(BLOCK_ITEM_SORT_ERROR);
    }
}
