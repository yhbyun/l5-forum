<?php

namespace App;

class OAuthSession extends Model
{
    protected $table = 'oauth_sessions';
    protected $fillable = [];

    public function user()
    {
        return $this->belongsTo('User', 'owner_id');
    }

    public function token()
    {
        return $this->hasOne('AccessToken', 'session_id');
    }
}
