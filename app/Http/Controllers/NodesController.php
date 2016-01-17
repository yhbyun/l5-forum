<?php

namespace App\Http\Controllers;

use App\Node;
use App\Topic;

class NodesController extends Controller
{

    protected $topic;

    public function __construct(Topic $topic)
    {
        $this->topic = $topic;
    }

    public function show($id)
    {
        $node = Node::findOrFail($id);
        $filter = $this->topic->present()->getTopicFilter();
        $topics = $this->topic->getNodeTopicsWithFilter($filter, $id);

        return view('topics.index', compact('topics', 'node'));
    }
}
