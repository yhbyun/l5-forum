<?php

namespace App;

use Cache;

class Stat
{
    const CACHE_KEY     = 'site_stat';
    const CACHE_MINUTES = 10;

    public $topicCount;
    public $replyCount;
    public $userCount;

    public static function getSiteStat()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_MINUTES, function () {
            $entity = new self();
            $entity->topicCount = Topic::count();
            $entity->replyCount = Reply::count();
            $entity->userCount  = User::count();

            return $entity;
        });
    }
}
