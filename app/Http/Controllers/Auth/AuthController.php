<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Listeners\GithubAuthenticatorListener;
use App\Listeners\UserCreatorListener;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laracasts\Flash\Flash;
use Laravel\Socialite\Facades\Socialite;
use Validator;

class AuthController extends Controller implements GithubAuthenticatorListener, UserCreatorListener
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    public function getLogout()
    {
        auth()->logout();
        Flash::success(lang('Operation succeeded.'));

        return redirect()->route('home');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $githubUser = Socialite::driver('github')->user();

        return app('App\Services\GithubAuthenticator')->authByCode($this, $githubUser);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        if (! session()->has('githubUser')) {
            return redirect()->route('login');
        }

        $githubUser = array_merge((array) session('githubUser'), session('_old_input', []));

        return view('auth.signupconfirm', compact('githubUser'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        if (! session()->has('githubUser')) {
            return redirect()->route('login');
        }

        $request['github_id'] = session('githubUser')->id;

        return app('App\Services\Creators\UserCreator')->create($this, $request->all());
    }

    /**
     * ----------------------------------------
     * UserCreatorListener Delegate
     * ----------------------------------------
     */

    public function userValidationError($errors)
    {
        return redirect()->to('/');
    }

    public function userCreated($user)
    {
        auth()->login($user, true);
        session()->forget('githubUser');

        Flash::success(lang('Congratulations and Welcome!'));

        return redirect()->intended();
    }

    /**
     * ----------------------------------------
     * GithubAuthenticatorListener Delegate
     * ----------------------------------------
     */

    public function userFound($user)
    {
        auth()->login($user, true);
        session()->forget('githubUser');

        Flash::success(lang('Login Successfully.'));

        return redirect()->intended();
    }

    public function userIsBanned($user)
    {
        return redirect()->route('user-banned');
    }

    public function userNotFound($githubUser)
    {
        session()->put('githubUser', $githubUser);

        return redirect()->route('signup');
    }
}
