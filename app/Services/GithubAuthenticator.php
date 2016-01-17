<?php

namespace App\Services;

use App\Listeners\GithubAuthenticatorListener;
use App\User;

/**
 * This class can call the following methods on the listener object:
 *
 * userFound($user)
 * userIsBanned($user)
 * userNotFound($githubData)
 */
class GithubAuthenticator
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function authByCode(GithubAuthenticatorListener $listener, $githubUser)
    {
        $user = $this->user->getByGithubId($githubUser->id);
        if ($user) {
            return $this->loginUser($listener, $user);
        }

        return $listener->userNotFound($githubUser);
    }

    private function loginUser($listener, $user)
    {
        if ($user->is_banned) {
            return $listener->userIsBanned($user);
        }

        return $listener->userFound($user);
    }
}
