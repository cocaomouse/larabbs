<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Queries\ReplyQuery;
use App\Http\Resources\ReplyResource;

class RepliesController extends Controller
{
    /**
     * 话题的回复列表
     *
     * @param $topicId
     * @param ReplyQuery $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($topicId, ReplyQuery $query)
    {
        $replies = $query->where('topic_id', $topicId)->paginate();

        return ReplyResource::collection($replies);
    }

    /**
     * 某个用户的回复列表
     *
     * @param $userId
     * @param ReplyQuery $query
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function userIndex($userId, ReplyQuery $query)
    {
        $replies = $query->where('user_id', $userId)->paginate();

        return ReplyResource::collection($replies);
    }
}