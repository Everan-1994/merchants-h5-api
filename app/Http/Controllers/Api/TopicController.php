<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ApiResources\TopicResource;
use App\Models\Comment;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller;

class TopicController extends Controller
{
    /**
     * 话题列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $page_size = $request->input('page_size', 10);

        $topics = Topic::query()
            ->where('status', Topic::ACTIVE)
            ->with('comments')
            ->orderBy('sort', 'desc')
            ->paginate($page_size, ['*'], 'page', $page);

        return response()->json([
            'data'  => TopicResource::collection($topics),
            'total' => $topics->total(),
        ]);
    }

    /**
     * 评论话题
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function commentTopic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic_id' => 'required|numeric',
            'comment'  => 'required',
        ], [
            'topic_id.required' => '话题id不能为空',
            'topic_id.numeric'  => '话题id不能为字符串',
            'comment.required'  => '请填写评论内容',
        ]);

        if ($validator->fails()) {
            return response([
                'code'    => 1,
                'message' => '评论信息有误',
                'errors'  => $validator->errors(),
            ]);
        }

        $comment_info = [
            'user_id'  => Auth::guard('user')->user()->id,
            'topic_id' => $request->input('topic_id'),
            'comment'  => $request->input('comment')
        ];

        try {
            // 评论信息
            Comment::query()->create($comment_info);

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
     * 删除评论信息
     * @param $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function deleteTopicCommentById($id)
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

        $comment = Comment::query()->where('id', $id);

        if ($comment->value('user_id')
            !== Auth::guard('user')->user()->id) {
            return response([
                'code'    => 1,
                'message' => '不能删除非本人的评论',
            ]);
        }

        try {
            // 删除评论信息
            $comment->delete();

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
     * 话题详情
     * @param $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function getTopicDetail($id)
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

        $builder = Topic::query();

        if (!$builder->where('id', $id)->exists()) {
            return response([
                'code'    => 1,
                'message' => '没有找到对应的话题',
            ]);
        }

        $topic_info = $builder->where('status', Topic::ACTIVE)
            ->with(['comments'])
            ->orderBy('sort', 'desc')
            ->find($id);

        return response(new TopicResource($topic_info));
    }

}