<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Queries\ReplyQuery;
use App\Http\Requests\Api\ReplyRequest;
use App\Http\Resources\ReplyResource;
use App\Models\Reply;
use App\Models\Topic;

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

    /**
     * 发表回复
     *
     * @param ReplyRequest $request
     * @param Topic $topic
     * @param Reply $reply
     */
    public function store(ReplyRequest $request, Topic $topic, Reply $reply)
    {
        $reply->content = $request['content'];
        $reply->topic()->associate($topic);
        $reply->user()->associate($request->user());
        $reply->save();

        return new ReplyResource($reply);
    }
}
