<?php

namespace App\Http\Controllers;

use App\Exceptions\ManageTopicsException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    public function authorOrAdminPermissioinRequire($author_id)
    {
        if (! app('entrust')->can('manage_topics') && $author_id != auth()->id()) {
            throw new ManageTopicsException("permission-required");
        }
    }
}
