<?php

namespace App\Services\Creators;

use App\Listeners\UserCreatorListener;
use App\User;

/**
 * This class can call the following methods on the observer object:
 *
 * userValidationError($errors)
 * userCreated($user)
 */
class UserCreator
{
    public function create(UserCreatorListener $observer, $data)
    {
        // Validation
        app('App\Http\Requests\SignupRequest');

        return $this->createValidUserRecord($observer, $data);
    }

    private function createValidUserRecord($observer, $data)
    {
        $user = User::create($data);
        if (!$user) {
            return $observer->userValidationError($user->getErrors());
        }

        return $observer->userCreated($user);
    }
}
