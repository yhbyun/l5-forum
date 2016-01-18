<?php namespace App\Listeners;

use App\Events\TopicWasViewed;
use App\Topic;
use Illuminate\Session\Store;

class UpdateTopicView
{
    /**
     * Session store instance.
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * Create a new view topic handler instance.
     *
     * @param Store $session
     */
    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Handle the view topic event.
     *
     * @param TopicWasViewed $event
     */
    public function handle(TopicWasViewed $event)
    {
        $topic = $event->topic;

        if (! $this->hasViewedTopic($topic)) {
            $topic->increment('view_count');

            $this->storeViewedTopic($topic);
        }
    }

    /**
     * Determine whether the user has viewed the topic.
     *
     * @param Topic $topic
     * @return bool
     */
    protected function hasViewedTopic(Topic $topic)
    {
        return array_key_exists($topic->ID, $this->getViewedTopics());
    }

    /**
     * Get the users viewed topic from the session.
     *
     * @return array
     */
    protected function getViewedTopics()
    {
        return $this->session->get('viewed_topics', []);
    }

    /**
     * Append the newly viewed topic to the session.
     *
     * @param Topic $topic
     */
    protected function storeViewedTopic(Topic $topic)
    {
        $key = 'viewed_topics.' . $topic->ID;

        $this->session->put($key, time());
    }
}
