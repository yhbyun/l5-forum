<?php namespace App\Events;

use App\Topic;
use Illuminate\Queue\SerializesModels;

class TopicWasViewed extends Event
{
    use SerializesModels;

    /**
     * @var Topic
     */
    public $topic;

    /**
     * Create a new event instance.
     *
     * @param Topic $topic
     */
    public function __construct(Topic $topic)
    {
        $this->topic = $topic;
    }
}
