<?php

namespace App\Http\Controllers;

use App\Attention;
use App\Notification;
use App\Topic;
use Laracasts\Flash\Flash;

class AttentionsController extends Controller
{
    public function createOrDelete($id)
    {
        $topic = Topic::find($id);

        if (Attention::isUserAttentedTopic(auth()->user(), $topic)) {
            $message = lang('Successfully remove attention.');
            auth()->user()->attentTopics()->detach($topic->id);
        } else {
            $message = lang('Successfully_attention');
            auth()->user()->attentTopics()->attach($topic->id);

            Notification::notify('topic_attent', auth()->user(), $topic->user, $topic);
        }

        Flash::success($message);

        return redirect()->route('topics.show', $topic->id);
    }
}
