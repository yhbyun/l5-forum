<?php

namespace App\Listeners;

interface GithubAuthenticatorListener
{
    public function userFound($user);
    public function userIsBanned($user);
    public function userNotFound($githubUser);
}
