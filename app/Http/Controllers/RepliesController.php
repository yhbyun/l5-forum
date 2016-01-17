<?php

namespace App\Http\Controllers;

use App\Listeners\CreatorListener;
use App\Reply;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

class RepliesController extends Controller implements CreatorListener
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $request['user_id'] = auth()->id();

        app('App\Http\Requests\CreateReplyRequest');

        return app('App\Services\Creators\ReplyCreator')->create($this, $request->except('_token'));
    }

    public function vote($id)
    {
        $reply = Reply::find($id);
        app('App\Services\Voter')->replyUpVote($reply);

        return redirect()->route('topics.show', [$reply->topic_id, '#reply'.$reply->id]);
    }

    public function destroy($id)
    {
        $reply = Reply::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($reply->user_id);
        $reply->delete();

        $reply->topic->decrement('reply_count', 1);

        Flash::success(lang('Operation succeeded.'));

        $reply->topic->generateLastReplyUserInfo();

        return redirect()->route('topics.show', $reply->topic_id);
    }

    /**
     * ----------------------------------------
     * CreatorListener Delegate
     * ----------------------------------------
     */

    public function creatorFailed($errors)
    {
        Flash::error(lang('Operation failed.'));
        return redirect()->back();
    }

    public function creatorSucceed($reply)
    {
        Flash::success(lang('Operation succeeded.'));
        return redirect()->route('topics.show', [request('topic_id'), '#reply'.$reply->id]);
    }
}
