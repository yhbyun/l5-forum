<?php

namespace App\Services;

use App\Notification;
use App\Reply;
use App\Topic;
use Laracasts\Flash\Flash;

class Voter
{
    public $notifiedUsers = [];

    public function topicUpVote(Topic $topic)
    {
        if ($topic->votes()->ByWhom(auth()->id())->WithType('upvote')->count()) {
            // click twice for remove upvote
            $topic->votes()->ByWhom(auth()->id())->WithType('upvote')->delete();
            $topic->decrement('vote_count', 1);
        } elseif ($topic->votes()->ByWhom(auth()->id())->WithType('downvote')->count()) {
            // user already clicked downvote once
            $topic->votes()->ByWhom(auth()->id())->WithType('downvote')->delete();
            $topic->votes()->create(['user_id' => auth()->id(), 'is' => 'upvote']);
            $topic->increment('vote_count', 2);
        } else {
            // first time click
            $topic->votes()->create(['user_id' => auth()->id(), 'is' => 'upvote']);
            $topic->increment('vote_count', 1);

            Notification::notify('topic_upvote', auth()->user(), $topic->user, $topic);
        }
    }

    public function topicDownVote(Topic $topic)
    {
        if ($topic->votes()->ByWhom(auth()->id())->WithType('downvote')->count()) {
            // click second time for remove downvote
            $topic->votes()->ByWhom(auth()->id())->WithType('downvote')->delete();
            $topic->increment('vote_count', 1);
        } elseif ($topic->votes()->ByWhom(auth()->id())->WithType('upvote')->count()) {
            // user already clicked upvote once
            $topic->votes()->ByWhom(auth()->id())->WithType('upvote')->delete();
            $topic->votes()->create(['user_id' => auth()->id(), 'is' => 'downvote']);
            $topic->decrement('vote_count', 2);
        } else {
            // click first time
            $topic->votes()->create(['user_id' => auth()->id(), 'is' => 'downvote']);
            $topic->decrement('vote_count', 1);
        }
    }

    public function replyUpVote(Reply $reply)
    {
        if (auth()->id() == $reply->user_id) {
            return Flash::warning(lang('Can not vote your feedback'));
        }

        if ($reply->votes()->ByWhom(auth()->id())->WithType('upvote')->count()) {
            // click twice for remove upvote
            $reply->votes()->ByWhom(auth()->id())->WithType('upvote')->delete();
            $reply->decrement('vote_count', 1);
        } elseif ($reply->votes()->ByWhom(auth()->id())->WithType('downvote')->count()) {
            // user already clicked downvote once
            $reply->votes()->ByWhom(auth()->id())->WithType('downvote')->delete();
            $reply->votes()->create(['user_id' => auth()->id(), 'is' => 'upvote']);
            $reply->increment('vote_count', 2);
        } else {
            // first time click
            $reply->votes()->create(['user_id' => auth()->id(), 'is' => 'upvote']);
            $reply->increment('vote_count', 1);

            Notification::notify('reply_upvote', auth()->user(), $reply->user, $reply->topic, $reply);
        }
    }
}
