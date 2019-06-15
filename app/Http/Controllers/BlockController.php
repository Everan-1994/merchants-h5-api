<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BlockController extends Controller
{
    /**
     * Get all block data.
     */
    public function readAll()
    {
        $items = Block::query()->select('id', 'name', 'front_cover', 'watch_times', 'status', 'createdAt', 'updatedAt')->orderBy('createdAt', 'desc')->get();

        return $this->success($items->toArray());
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function read($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int',
        ]);

        $item = Block::query()->select('id', 'name', 'front_cover', 'watch_times', 'status', 'createdAt', 'updatedAt')->whereId($id)->first();

        if ($item) {
            return $this->success($item->toArray());
        } else {
            $this->throwExeptionByCode('视频模块不存在');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Request $request)
    {
        $params = $this->validate($request, [
            'ids'   => 'required|array|min:1',
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
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function createOrUpdate(Request $request, $id = null)
    {
        $params = $this->validate($request, [
            'name' => 'required|string|max:255',
        ]);
        if (!empty($id)) {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|int',
            ]);
            $params['id'] = $id;
        }

        if (isset($params['id']) && $params['id']) {
            $block = Block::query()->find($params['id']);
            if (!$block) {
                $this->throwExeptionByCode('视频模块不存在');
            }
        } else {
            $block = new Block();
        }
        $block->fill($params);
        $result = $block->save();
        if ($result) {
            return $this->success(['id' => $block->id]);
        } else {
            $this->throwExeptionByCode('创建或更新失败');
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
            'max'  => 'int',
        ]);
        try {
            $block = Block::query()
                ->where('flag', '=', $params['flag'])
                ->firstOrFail();
        } catch (ModelNotFoundException  $e) {
            return $this->fail('视频模块不存在');
        }

        $items = $block
            ->select('id', 'blockId', 'title', 'description', 'url', 'imagePath', 'sort')
            ->take(isset($params['max']) ? $params['max'] : 99)
            ->get();

        return $this->success($items->toArray());
    }
}
