<?php namespace App\Services\Creators;

use App\Listeners\CreatorListener;
use App\Topic;
use Carbon\Carbon;

class TopicCreator
{
    public function create(CreatorListener $observer, $data)
    {
        $data['user_id'] = auth()->id();
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();

        $data['body_original'] = $data['body'];
        $data['body'] = app('markdown')->convertMarkdownToHtml($data['body']);
        $data['excerpt'] = Topic::makeExcerpt($data['body']);

        $topic = Topic::create($data);
        if (! $topic) {
            return $observer->creatorFailed($topic->getErrors());
        }

        auth()->user()->increment('topic_count', 1);

        //TODO
        //Robot::notify($data['body_original'], 'Topic', $topic, Auth::user());

        return $observer->creatorSucceed($topic);
    }
}
