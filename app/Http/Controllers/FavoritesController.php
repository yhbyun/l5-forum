<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Notification;
use App\Topic;
use Laracasts\Flash\Flash;

class FavoritesController extends Controller
{
    public function createOrDelete($id)
    {
        $topic = Topic::find($id);

        if (Favorite::isUserFavoritedTopic(auth()->user(), $topic)) {
            auth()->user()->favoriteTopics()->detach($topic->id);
        } else {
            auth()->user()->favoriteTopics()->attach($topic->id);
            Notification::notify('topic_favorite', auth()->user(), $topic->user, $topic);
        }

        Flash::success(lang('Operation succeeded.'));

        return redirect()->route('topics.show', $topic->id);
    }
}
