<?php

namespace App\Presenters;

use Laracasts\Presenter\Presenter;

class TopicPresenter extends Presenter
{
    public function topicFilter($filter)
    {
        $node_id = request()->segment(2);
        $node_append = '';
        if ($node_id) {
            $link = url('nodes', $node_id) . '?filter=' . $filter;
        } else {
            $query_append = '';
            $query = request()->except('filter', '_pjax');
            if ($query) {
                $query_append = '&'.http_build_query($query);
            }
            $link = url('topics') . '?filter=' . $filter . $query_append . $node_append;
        }
        $selected = request('filter') ? (request('filter') == $filter ? ' class="selected"':'') : '';

        return 'href="' . $link . '"' . $selected;
    }

    public function getTopicFilter()
    {
        $filters = ['noreply', 'vote', 'excellent','recent'];
        $request_filter = request('filter');
        if (in_array($request_filter, $filters)) {
            return $request_filter;
        }

        return 'default';
    }

    public function haveDefaultNode($node, $snode)
    {
        if (count($node) && ($snode && $node->id == $snode->id)) {
            return true;
        }

        if (old('node_id') && ($snode && old('node_id') == $snode->id)) {
            return true;
        }

        return false;
    }

    public function voteState($vote_type)
    {
        if ($this->votes()->ByWhom(auth()->id())->WithType($vote_type)->count()) {
            return 'active';
        }
    }

    public function replyFloorFromIndex($index)
    {
        $index += 1;
        $current_page = request('page') ?: 1;
        return ($current_page - 1) * config('phphub.replies_perpage') + $index;
    }
}
