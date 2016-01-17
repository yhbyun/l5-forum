<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, EntrustUserTrait;

    use PresentableTrait;
    protected $presenter = \App\Presenters\UserPresenter::class;

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $hidden = ['github_id', 'password', 'remember_token'];
    protected $guarded = ['id', 'notifications', 'is_banned'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($topic) {
            SiteStatus::newUser();
        });
    }

    public function favoriteTopics()
    {
        return $this->belongsToMany('App\Topic', 'favorites')->withTimestamps();
    }

    public function attentTopics()
    {
        return $this->belongsToMany('App\Topic', 'attentions')->withTimestamps();
    }

    public function topics()
    {
        return $this->hasMany('App\Topic');
    }

    public function replies()
    {
        return $this->hasMany('App\Reply');
    }

    public function notifications()
    {
        return $this->hasMany('App\Notification')->recent()->with('topic', 'fromUser')->paginate(20);
    }

    public function getByGithubId($id)
    {
        return $this->where('github_id', $id)->first();
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
