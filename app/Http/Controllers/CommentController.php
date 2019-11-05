<?php

namespace App\Http\Controllers;

use App\Http\Resources\Admin\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * @param $topicId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($topicId, Request $request)
    {
        $comment = Comment::query()
            ->where('topic_id', $topicId)
            ->when($request->input('startTime') && $request->input('endTime'), function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    date('Y-m-d H:i:s', $request->input('startTime')),
                    date('Y-m-d H:i:s', $request->input('endTime')),
                ]);
            })
            ->when($request->filled('name'), function ($query) use ($request) {
                $query->WhereHas('user', function ($query) use ($request) {
                    $query->where('name', 'like', '%'.$request->input('name').'%');
                });
            })
            ->orderBy($request->input('order') ?: 'created_at', $request->input('sort') ?: 'desc')
            ->paginate($request->input('pageSize', 10), ['*'], 'page', $request->input('page', 1));

        return $this->success([
            'data'  => CommentResource::collection($comment),
            'total' => $comment->total(),
        ]);
    }

    /**
     * 删除.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Request $request)
    {
        $params = $this->validate($request, [
            'ids'   => 'required|array|min:1',
            'ids.*' => 'required|distinct|int',
        ]);

        $num = count($params['ids']);
        $numDestroied = Comment::query()
            ->whereIn('id', collect($params['ids']))
            ->delete();

        if ($num == $numDestroied) {
            return $this->success([], '删除成功');
        }

        return $this->fail('删除失败');
    }
}