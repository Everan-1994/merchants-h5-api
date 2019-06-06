<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class BlockController extends Controller
{
    /**
     * Get all block data.
     */
    public function readAll()
    {
        $items = Block::select('id', 'name', 'description', 'flag', 'createdAt', 'updatedAt')->orderBy('createdAt', 'desc')->get();

        return $this->success($items->toArray());
    }

    /**
     * Get the detail data of the block.
     *
     * @param Request $request
     * @param int     $id      :     block id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read(Request $request, $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int',
        ]);
        $item = Block::select('id', 'name', 'description', 'flag', 'createdAt', 'updatedAt')->whereId($id)->first();
        if ($item) {
            return $this->success($item->toArray());
        } else {
            $this->throwExeptionByCode(BLOCK_NOT_EXIST);
        }
    }

    /**
     * Delete blocks.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $params = $this->validate($request, [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|distinct|int',
        ]);
        $num = count($params['ids']);
        DB::beginTransaction();
        $numDestroied = Block::destroy($params['ids']);
        if ($num == $numDestroied) {
            DB::commit();

            return $this->success();
        } else {
            DB::rollBack();
            $this->throwExeptionByCode(BLOCK_DELETE_ERROR);
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
            'name' => 'required|string|max:255',
            'description' => 'string',
            'flag' => 'required|string|max:255|uniqueSoftDelete:blocks,flag'.($id ? ",{$id},id" : ''),
        ]);
        if (!empty($id)) {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|int',
            ]);
            $params['id'] = $id;
        }

        if (isset($params['id']) && $params['id']) {
            $block = Block::find($params['id']);
            if (!$block) {
                $this->throwExeptionByCode(BLOCK_NOT_EXIST);
            }
        } else {
            $block = new Block();
        }
        $block->fill($params);
        $result = $block->save();
        if ($result) {
            return $this->success(['id' => $block->id]);
        } else {
            $this->throwExeptionByCode(BLOCK_CREATE_OR_UPDATE_ERROR);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function queryBlock(Request $request)
    {
        $params = $this->validate($request, [
            'flag' => 'required|string|max:255',
            'max' => 'int',
        ]);
        try {
            $block = Block::query()
                ->where('flag', '=', $params['flag'])
                ->firstOrFail();
        } catch (ModelNotFoundException  $e) {
            return $this->fail(BLOCK_NOT_EXIST);
        }

        $items = $block
            ->sortedItems()
            ->select('id', 'blockId', 'title', 'description', 'url', 'imagePath', 'sort')
            ->take(isset($params['max']) ? $params['max'] : 99)
            ->get();

        return $this->success($items->toArray());
    }
}
