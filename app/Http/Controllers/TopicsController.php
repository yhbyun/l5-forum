<?php

namespace App\Http\Controllers;

use App\Append;
use App\Events\TopicWasViewed;
use App\Http\Requests\CreateTopicRequest;
use App\Link;
use App\Listeners\CreatorListener;
use App\Node;
use App\Notification;
use App\Topic;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

class TopicsController extends Controller implements CreatorListener
{
    protected $topic;

    public function __construct(Topic $topic)
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->topic = $topic;
    }

    public function index()
    {
        $filter = $this->topic->present()->getTopicFilter();
        $topics = $this->topic->getTopicsWithFilter($filter);
        $nodes  = Node::allLevelUp();
        $links  = Link::remember(1440)->get();

        return view('topics.index', compact('topics', 'nodes', 'links'));
    }

    public function create(Request $request)
    {
        $node = Node::find($request->input('node_id'));
        $nodes = Node::allLevelUp();

        return view('topics.create_edit', compact('nodes', 'node'));
    }

    public function store(CreateTopicRequest $request)
    {
        return app('App\Services\Creators\TopicCreator')->create($this, $request->except('_token'));
    }

    public function show($id)
    {
        $topic = Topic::findOrFail($id);
        $replies = $topic->getRepliesWithLimit(config('l5forum.replies_perpage', 20));
        $node = $topic->node;
        $nodeTopics = $topic->getSameNodeTopics();

        event(new TopicWasViewed($topic));

        return view('topics.show', compact('topic', 'replies', 'nodeTopics', 'node'));
    }

    public function edit($id)
    {
        $topic = Topic::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($topic->user_id);
        $nodes = Node::allLevelUp();
        $node = $topic->node;

        $topic->body = $topic->body_original;

        return view('topics.create_edit', compact('topic', 'nodes', 'node'));
    }

    public function append(Request $request, $id)
    {
        $topic = Topic::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($topic->user_id);

        $markdown = app('markdown');
        $content = $markdown->convertMarkdownToHtml($request->input('content'));

        $append = Append::create(['topic_id' => $topic->id, 'content' => $content]);

        //TODO
        //App::make('Phphub\Notification\Notifier')->newAppendNotify(Auth::user(), $topic, $append);

        Flash::success(lang('Operation succeeded.'));

        return redirect()->route('topics.show', $topic->id);
    }

    public function update(CreateTopicRequest $request, $id)
    {
        $topic = Topic::findOrFail($id);
        $data = $request->only('title', 'body', 'node_id');

        $this->authorOrAdminPermissioinRequire($topic->user_id);

        $markdown = app('markdown');
        $data['body_original'] = $data['body'];
        $data['body'] = $markdown->convertMarkdownToHtml($data['body']);
        $data['excerpt'] = Topic::makeExcerpt($data['body']);
        $topic->update($data);

        Flash::success(lang('Operation succeeded.'));

        return redirect()->route('topics.show', $topic->id);
    }

    /**
     * ----------------------------------------
     * User Topic Vote function
     * ----------------------------------------
     */

    public function upvote($id)
    {
        $topic = Topic::find($id);
        app('App\Services\Voter')->topicUpVote($topic);

        return redirect()->route('topics.show', $topic->id);
    }

    public function downvote($id)
    {
        $topic = Topic::find($id);
        app('App\Services\Voter')->topicDownVote($topic);

        return redirect()->route('topics.show', $topic->id);
    }

    /**
     * ----------------------------------------
     * Admin Topic Management
     * ----------------------------------------
     */

    public function recomend($id)
    {
        $topic = Topic::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($topic->user_id);
        $topic->is_excellent = (!$topic->is_excellent);
        $topic->save();

        Flash::success(lang('Operation succeeded.'));
        Notification::notify('topic_mark_excellent', auth()->user(), $topic->user, $topic);

        return redirect()->route('topics.show', $topic->id);
    }

    public function wiki($id)
    {
        $topic = Topic::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($topic->user_id);
        $topic->is_wiki = (!$topic->is_wiki);
        $topic->save();

        Flash::success(lang('Operation succeeded.'));
        Notification::notify('topic_mark_wiki', auth()->user(), $topic->user, $topic);

        return redirect()->route('topics.show', $topic->id);
    }

    public function pin($id)
    {
        $topic = Topic::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($topic->user_id);
        ($topic->order > 0) ? $topic->decrement('order', 1) : $topic->increment('order', 1);

        return redirect()->route('topics.show', $topic->id);
    }

    public function sink($id)
    {
        $topic = Topic::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($topic->user_id);
        ($topic->order >= 0) ? $topic->decrement('order', 1) : $topic->increment('order', 1);

        return redirect()->route('topics.show', $topic->id);
    }

    public function destroy($id)
    {
        $topic = Topic::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($topic->user_id);
        $topic->delete();

        Flash::success(lang('Operation succeeded.'));

        return redirect()->route('topics.index');
    }

    public function uploadImage()
    {
        if ($file = Input::file('file')) {
            $allowed_extensions = ["png", "jpg", "gif"];
            if ($file->getClientOriginalExtension() && !in_array($file->getClientOriginalExtension(), $allowed_extensions)) {
                return ['error' => 'You may only upload png, jpg or gif.'];
            }

            $fileName        = $file->getClientOriginalName();
            $extension       = $file->getClientOriginalExtension() ?: 'png';
            $folderName      = 'uploads/images/' . date("Ym", time()) .'/'.date("d", time()) .'/'. Auth::user()->id;
            $destinationPath = public_path() . '/' . $folderName;
            $safeName        = str_random(10).'.'.$extension;
            $file->move($destinationPath, $safeName);

            // If is not gif file, we will try to reduse the file size
            if ($file->getClientOriginalExtension() != 'gif') {
                // open an image file
                $img = Image::make($destinationPath . '/' . $safeName);
                // prevent possible upsizing
                $img->resize(1440, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                // finally we save the image as a new file
                $img->save();
            }

            $data['filename'] = getUserStaticDomain() . $folderName .'/'. $safeName;

            SiteStatus::newImage();
        } else {
            $data['error'] = 'Error while uploading file';
        }
        return $data;
    }

    /**
     * ----------------------------------------
     * CreatorListener Delegate
     * ----------------------------------------
     */

    public function creatorFailed($errors)
    {
        return redirect()->to('/');
    }

    public function creatorSucceed($topic)
    {
        Flash::success(lang('Operation succeeded.'));

        return redirect()->route('topics.show', [$topic->id]);
    }
}
