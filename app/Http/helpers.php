<?php

function cdn($filepath)
{
    if (config('app.url_static')) {
        return config('app.url_static') . $filepath;
    } else {
        return config('app.url') . $filepath;
    }
}

function getCdnDomain()
{
    return config('app.url_static') ?: config('app.url');
}

function getUserStaticDomain()
{
    return config('app.user_static') ?: config('app.url');
}

function lang($text)
{
    return str_replace('l5forum.', '', trans('l5forum.'.$text));
}
