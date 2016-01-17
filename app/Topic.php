<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\Paginator;
use Laracasts\Presenter\PresentableTrait;

class Topic extends Model
{
    use PresentableTrait;
    protected $presenter = \App\Presenters\TopicPresenter::class;

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public $timestamps = false;

    protected $fillable = [
        'title',
        'body',
        'excerpt',
        'body_original',
        'user_id',
        'node_id',
        'created_at',
        'updated_at'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($topic) {
            SiteStatus::newTopic();
        });
    }

    public function votes()
    {
        return $this->morphMany('App\Vote', 'votable');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany('App\User', 'favorites');
    }

    public function attentedBy()
    {
        return $this->belongsToMany('App\User', 'attentions');
    }

    public function node()
    {
        return $this->belongsTo('App\Node');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function lastReplyUser()
    {
        return $this->belongsTo('App\User', 'last_reply_user_id');
    }

    public function replies()
    {
        return $this->hasMany('App\Reply');
    }

    public function appends()
    {
        return $this->hasMany('App\Append');
    }

    public function getWikiList()
    {
        return $this->where('is_wiki', '=', true)->orderBy('created_at', 'desc')->get();
    }

    public function generateLastReplyUserInfo()
    {
        $lastReply = $this->replies()->recent()->first();

        $this->last_reply_user_id = $lastReply ? $lastReply->user_id : 0;
        $this->save();
    }

    public function getRepliesWithLimit($limit = 30)
    {
        Paginator::currentPageResolver(function ($pageName) use ($limit) {
            $page = app('request')->input($pageName);

            if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int) $page >= 1) {
                return $page;
            }

            return ceil($this->reply_count / $limit);
        });

        return $this->replies()
                    ->orderBy('created_at', 'asc')
                    ->with('user')
                    ->paginate($limit);
    }

    public function getTopicsWithFilter($filter, $limit = 20)
    {
        return $this->applyFilter($filter)
                    ->with('user', 'node', 'lastReplyUser')
                    ->paginate($limit);
    }

    public function getNodeTopicsWithFilter($filter, $node_id, $limit = 20)
    {
        return $this->applyFilter($filter == 'default' ? 'node' : $filter)
                    ->where('node_id', '=', $node_id)
                    ->with('user', 'node', 'lastReplyUser')
                    ->paginate($limit);
    }

    public function applyFilter($filter)
    {
        switch ($filter) {
            case 'noreply':
                return $this->orderBy('reply_count', 'asc')->recent();
                break;

            case 'vote':
                return $this->orderBy('vote_count', 'desc')->recent();
                break;

            case 'excellent':
                return $this->excellent()->recent();
                break;

            case 'recent':
                return $this->recent();
                break;

            case 'node':
                return $this->recentReply();
                break;

            default:
                return $this->pinAndRecentReply();
                break;
        }
    }

    public function getSameNodeTopics($limit = 8)
    {
        return Topic::where('node_id', '=', $this->node_id)
                        ->recent()
                        ->take($limit)
                        ->remember(10)
                        ->get();
    }

    public function scopeWhose($query, $user_id)
    {
        return $query->where('user_id', '=', $user_id)->with('node');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopePinAndRecentReply($query)
    {
        return $query->whereRaw("(`created_at` > '".Carbon::today()->subMonth()->toDateString()."' or (`order` > 0) )")
                     ->orderBy('order', 'desc')
                     ->orderBy('updated_at', 'desc');
    }

    public function scopeRecentReply($query)
    {
        return $query->orderBy('order', 'desc')
                     ->orderBy('updated_at', 'desc');
    }

    public function scopeExcellent($query)
    {
        return $query->where('is_excellent', '=', true);
    }

    public static function makeExcerpt($body)
    {
        $html = $body;
        $excerpt = trim(preg_replace('/\s\s+/', ' ', strip_tags($html)));
        return str_limit($excerpt, 200);
    }
}
