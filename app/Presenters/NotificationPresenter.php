<?php

namespace App\Presenters;

use Laracasts\Presenter\Presenter;

class NotificationPresenter extends Presenter
{
    public function labelUp()
    {
        switch ($this->type) {
            case 'new_reply':
                $label = lang('Your topic have new reply:');
                break;

            case 'attention':
                $label = lang('Attented topic has new reply:');
                break;

            case 'at':
                $label = lang('Mention you At:');
                break;

            case 'topic_favorite':
                $label = lang('Favorited your topic:');
                break;

            case 'topic_attent':
                $label = lang('Attented your topic:');
                break;

            case 'topic_upvote':
                $label = lang('Up Vote your topic');
                break;

            case 'reply_upvote':
                $label = lang('Up Vote your reply');
                break;

            case 'topic_mark_wiki':
                $label = lang('has mark your topic as wiki:');
                break;

            case 'topic_mark_excellent':
                $label = lang('has recomended your topic:');
                break;

            case 'comment_append':
                $label = lang('Commented topic has new update:');
                break;

            case 'attention_append':
                $label = lang('Attented topic has new update:');
                break;

            default:
                $label = '';
                break;
        }

        return $label;
    }
}
