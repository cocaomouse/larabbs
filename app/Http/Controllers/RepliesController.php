<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReplyRequest;
use App\Models\Reply;
use Auth;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(ReplyRequest $request, Reply $reply)
    {
        $reply->content = $request->input('content');
        $reply->user_id = Auth::id();
        $reply->topic_id = $request->topic_id;
        $re_result = $reply->save();

        if (!$re_result) {
            return redirect()->to($reply->topic->link())->with('danger', '评论创建失败!');
        }

        return redirect()->to($reply->topic->link())->with('success', '评论创建成功!');
    }

    public function edit(Reply $reply)
    {
        $this->authorize('update', $reply);

        return view('replies.create_and_edit', compact('reply'));
    }

    public function update(ReplyRequest $request, Reply $reply)
    {
        $this->authorize('update', $reply);
        $reply->update($request->all());

        return redirect()->route('replies.show', $reply->id)->with('message', 'Updated successfully.');
    }

    public function destroy(Reply $reply)
    {
        $this->authorize('destroy', $reply);
        $reply->delete();

        return redirect()->to($reply->topic->link())->with('success', '评论删除成功!');
    }
}
