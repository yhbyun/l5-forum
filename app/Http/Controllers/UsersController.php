<?php

namespace App\Http\Controllers;

use App\Exceptions\ManageTopicsException;
use App\Http\Requests\UpdateUserRequest;
use App\Reply;
use App\Topic;
use App\User;
use Laracasts\Flash\Flash;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['edit', 'update', 'destroy']]);
    }

    public function authorOrAdminPermissioinRequire($author_id)
    {
        if (! app('entrust')->can('manage_users') && $author_id != auth()->id()) {
            throw new ManageTopicsException("permission-required");
        }
    }

    public function index()
    {
        $users = User::recent()->take(48)->get();

        return view('users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $topics = Topic::whose($user->id)->recent()->limit(10)->get();
        $replies = Reply::whose($user->id)->recent()->limit(10)->get();

        return view('users.show', compact('user', 'topics', 'replies'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($user->id);

        return view('users.edit', compact('user', 'topics', 'replies'));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $this->authorOrAdminPermissioinRequire($user->id);
        $data = $request->only(
            'real_name',
            'twitter_account',
            'personal_website',
            'signature',
            'introduction'
        );

        $user->update($data);

        Flash::success(lang('Operation succeeded.'));

        return redirect()->route('users.show', $id);
    }

    public function destroy($id)
    {
        $this->authorOrAdminPermissioinRequire($topic->user_id);
    }

    public function replies($id)
    {
        $user = User::findOrFail($id);
        $replies = Reply::whose($user->id)->recent()->paginate(15);

        return view('users.replies', compact('user', 'replies'));
    }

    public function topics($id)
    {
        $user = User::findOrFail($id);
        $topics = Topic::whose($user->id)->recent()->paginate(15);

        return view('users.topics', compact('user', 'topics'));
    }

    public function favorites($id)
    {
        $user = User::findOrFail($id);
        $topics = $user->favoriteTopics()->paginate(15);

        return view('users.favorites', compact('user', 'topics'));
    }

    // TODO
    /*
    public function accessTokens($id)
    {
        if(!Auth::check() || Auth::id() != $id){
            return Redirect::route('users.show', $id);
        }
        $user = User::findOrFail($id);
        $sessions = OAuthSession::where([
            'owner_type' => 'user',
            'owner_id' => Auth::id(),
            ])
            ->with('token')
            ->lists('id') ?: [];

        $tokens = AccessToken::whereIn('session_id', $sessions)->get();

        return View::make('users.access_tokens', compact('user', 'tokens'));
    }

    public function revokeAccessToken($token)
    {
        $access_token = AccessToken::with('session')->find($token);

        if(!$access_token || !Auth::check() || $access_token->session->owner_id != Auth::id()){
            Flash::error(lang('Revoke Failed'));
        }else{
            $access_token->delete();
            Flash::success(lang('Revoke success'));
        }

        return Redirect::route('users.access_tokens', Auth::id());
    }
    */

    public function blocking($id)
    {
        $user = User::findOrFail($id);
        $user->is_banned = (!$user->is_banned);
        $user->save();

        return redirect()->route('users.show', $id);
    }

    // TODO
    /*
    public function githubApiProxy($username)
    {
        $cache_name = 'github_api_proxy_user_'.$username;

        //Cache 1 day
        return Cache::remember($cache_name, 1440, function () use ($username) {
            $result = (new GithubUserDataReader())->getDataFromUserName($username);
            return Response::json($result);
        });
    }

    public function githubCard()
    {
        return view('users.github-card');
    }

    public function refreshCache($id)
    {
        $user =  User::findOrFail($id);

        $user_info = (new GithubUserDataReader())->getDataFromUserName($user->github_name);

        // Refresh the GitHub card proxy cache.
        $cache_name = 'github_api_proxy_user_'.$user->github_name;
        Cache::put($cache_name, $user_info, 1440);

        // Refresh the avatar cache.
        $user->image_url = $user_info['avatar_url'];
        $user->cacheAvatar();
        $user->save();

        Flash::message(lang('Refresh cache success'));

        return Redirect::route('users.edit', $id);
    }

    public function regenerateLoginToken()
    {
        if(Auth::check()){
            Auth::user()->login_token = str_random(rand(20, 32));
            Auth::user()->save();
            Flash::success(lang('Regenerate succeeded.'));
        }else{
            Flash::error(lang('Regenerate failed.'));
        }

        return Redirect::route('users.show', Auth::id());
    }
    */
}
