<?php

namespace App;

use Carbon\Carbon;
use Laracasts\Presenter\PresentableTrait;

class Notification extends Model
{
    use PresentableTrait;
    public $presenter = \App\Presenters\NotificationPresenter::class;

    protected $fillable = [
            'from_user_id',
            'user_id',
            'topic_id',
            'reply_id',
            'body',
            'type'
            ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function topic()
    {
        return $this->belongsTo('App\Topic');
    }

    public function fromUser()
    {
        return $this->belongsTo('App\User', 'from_user_id');
    }

    /**
     * Create a notification
     *
     * @param string $type 'at', 'new_reply', 'attention', 'append'
     * @param User $fromUser come from who
     * @param array $users to who, array of users
     * @param Topic $topic current context
     * @param Reply|null $reply the content
     * @param null $content
     */
    public static function batchNotify($type, User $fromUser, $users, Topic $topic, Reply $reply = null, $content = null)
    {
        $nowTimestamp = Carbon::now();
        $data = [];

        foreach ($users as $toUser) {
            if ($fromUser->id == $toUser->id) {
                continue;
            }

            $data[] = [
                'from_user_id' => $fromUser->id,
                'user_id'      => $toUser->id,
                'topic_id'     => $topic->id,
                'reply_id'     => $content ?: $reply->id,
                'body'         => $content ?: $reply->body,
                'type'         => $type,
                'created_at'   => $nowTimestamp,
                'updated_at'   => $nowTimestamp
            ];

            $toUser->increment('notification_count', 1);
        }

        if (count($data)) {
            Notification::insert($data);
        }

        foreach ($data as $value) {
            self::pushNotification($value);
        }
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public static function notify($type, User $fromUser, User $toUser, Topic $topic, Reply $reply = null)
    {
        if ($fromUser->id == $toUser->id) {
            return;
        }

        if (Notification::isNotified($fromUser->id, $toUser->id, $topic->id, $type)) {
            return;
        }

        $nowTimestamp = Carbon::now();

        $data = [
            'from_user_id' => $fromUser->id,
            'user_id'      => $toUser->id,
            'topic_id'     => $topic->id,
            'reply_id'     => $reply ? $reply->id : 0,
            'body'         => $reply ? $reply->body : '',
            'type'         => $type,
            'created_at'   => $nowTimestamp,
            'updated_at'   => $nowTimestamp
        ];

        $toUser->increment('notification_count', 1);

        Notification::insert([$data]);
        self::pushNotification($data);
    }

    public static function pushNotification($data)
    {
        // TODO: implement
    }

    public static function isNotified($from_user_id, $user_id, $topic_id, $type)
    {
        $notifys = Notification::fromwhom($from_user_id)
                        ->toWhom($user_id)
                        ->atTopic($topic_id)
                        ->withType($type)->get();
        return $notifys->count();
    }

    public function scopeFromWhom($query, $from_user_id)
    {
        return $query->where('from_user_id', '=', $from_user_id);
    }

    public function scopeToWhom($query, $user_id)
    {
        return $query->where('user_id', '=', $user_id);
    }

    public function scopeWithType($query, $type)
    {
        return $query->where('type', '=', $type);
    }

    public function scopeAtTopic($query, $topic_id)
    {
        return $query->where('topic_id', '=', $topic_id);
    }
}
