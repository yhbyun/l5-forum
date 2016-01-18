<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;
use Illuminate\Config\Repository;

class TopicViewThrottle
{
    /**
     * Config repository instance.
     *
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Session store instance.
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * Create a new view throttle filter instance.
     *
     * @param  \Illuminate\Config\Repository  $config
     * @param  \Illuminate\Session\Store      $session
     */
    public function __construct(Repository $config, Store $session)
    {
        $this->config  = $config;
        $this->session = $session;
    }

    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $topics = $this->getViewedTopics();

        if ($topics !== null) {
            $topics = $this->purgeExpiredTopics($topics);

            $this->storeViewedTopics($topics);
        }

        return $next($request);
    }

    /**
     * Get the recently viewed topics from the session.
     *
     * @return array|null
     */
    protected function getViewedTopics()
    {
        return $this->session->get('viewed_topics', null);
    }

    /**
     * Get the view throttle time from the config.
     *
     * @return int
     */
    protected function getThrottleTime()
    {
        return $this->config->get('l5forum.view_throttle_time');
    }

    /**
     * Filter the topics array, removing expired topics.
     *
     * @param  array  $topics
     * @return array
     */
    protected function purgeExpiredTopics(array $topics)
    {
        $time         = time();
        $throttleTime = $this->getThrottleTime();

        return array_filter($topics, function ($timestamp) use ($time, $throttleTime) {
            return ($timestamp + $throttleTime) > $time;
        });
    }

    /**
     * Store the recently viewed topics in the session.
     *
     * @param  array  $topics
     * @return void
     */
    protected function storeViewedTopics(array $topics)
    {
        $this->session->put('viewed_topics', $topics);
    }
}
