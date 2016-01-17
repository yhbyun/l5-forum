<?php

namespace App\Services\Creators;

use App\Listeners\CreatorListener;
use App\Reply;
use App\Services\Notification\Mention;
use App\Topic;
use Carbon\Carbon;

class ReplyCreator
{
    protected $mentionParser;

    public function __construct(Mention $mentionParser)
    {
        $this->mentionParser = $mentionParser;
    }

    public function create(CreatorListener $observer, $data)
    {
        $data['user_id'] = auth()->id();
        $data['body'] = $this->mentionParser->parse($data['body']);

        $markdown = app('markdown');
        $data['body_original'] = $data['body'];
        $data['body'] = $markdown->convertMarkdownToHtml($data['body']);

        $reply = Reply::create($data);
        if (! $reply) {
            return $observer->creatorFailed($reply->getErrors());
        }

        // Add the reply user
        $topic = Topic::find($data['topic_id']);
        $topic->last_reply_user_id = auth()->id();
        $topic->reply_count++;
        $topic->updated_at = Carbon::now();
        $topic->save();

        auth()->user()->increment('reply_count', 1);

        // TODO
        //App::make('Phphub\Notification\Notifier')->newReplyNotify(Auth::user(), $this->mentionParser, $topic, $reply);

        //Robot::notify($data['body_original'], 'Reply', $topic, Auth::user());

        return $observer->creatorSucceed($reply);
    }
}
