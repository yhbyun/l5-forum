<?php

namespace App\Listeners;

interface CreatorListener
{
    public function creatorFailed($errors);
    public function creatorSucceed($model);
}
