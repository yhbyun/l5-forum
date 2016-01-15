<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    protected $table = 'oauth_access_tokens';
    protected $fillable = [];

    public function session()
    {
        return $this->belongsTo('OAuthSession', 'session_id');
    }
}
