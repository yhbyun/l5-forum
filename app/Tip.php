<?php

namespace App;

use Cache;

class Tip extends Model
{
    const CACHE_KEY = 'site_tips';
    const CACHE_MINUTES = 1440;

    protected $fillable = ['body'];

    public static function getRandTip()
    {
        $tips =  Cache::remember(self::CACHE_KEY, self::CACHE_MINUTES, function () {
            return Tip::all();
        });

        return $tips->count() > 0 ? $tips->random() : null;
    }
}
