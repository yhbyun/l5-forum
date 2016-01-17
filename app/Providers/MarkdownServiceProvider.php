<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MarkdownServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton('markdown', function ($app) {
            return new \App\Services\Markdown;
        });
    }

    public function provides()
    {
        return ['markdown'];
    }
}
