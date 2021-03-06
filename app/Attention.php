<?php

namespace App;

class Attention extends Model
{
    protected $fillable = [];

    public function post()
    {
        return $this->belongsTo('App\Post');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public static function isUserAttentedTopic(User $user, Topic $topic)
    {
        return Attention::where('user_id', $user->id)
                        ->where('topic_id', $topic->id)
                        ->first();
    }
}
