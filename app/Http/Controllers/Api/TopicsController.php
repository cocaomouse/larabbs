<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Queries\TopicQuery;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;

class TopicsController extends Controller
{
    /**
     * 话题列表
     *
     * @param Request $request
     * @param TopicQuery $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, TopicQuery $query)
    {
        $topics = $query->paginate();

        return TopicResource::collection($topics);
    }

    /**
     * 话题详情
     *
     * @param $topicId
     * @param TopicQuery $query
     * @return TopicResource
     */
    public function show($topicId, TopicQuery $query)
    {
        $topic = $query->findOrFail($topicId);

        return new TopicResource($topic);
    }

    /**
     * 用户发布的话题
     *
     * @param Request $request
     * @param User $user
     * @param TopicQuery $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function userIndex(Request $request, User $user, TopicQuery $query)
    {
        $topics = $query->where('user_id', $user->id)->paginate();

        return TopicResource::collection($topics);
    }

    /**
     * 用户删除发布的话题
     *
     * @param Topic $topic
     * @return \Illuminate\Http\JsonResponse|object
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);
        $topic->delete();

        return response()->json([
            'message' => '删除成功',
        ])->setStatusCode(201);
    }
}
