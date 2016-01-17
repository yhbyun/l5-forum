<?php

namespace App\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class LocalServiceProvider extends ServiceProvider
{
    /**
     * List of Local Enviroment Providers
     * @var array
     */
    protected $localProviders = [
        \Barryvdh\Debugbar\ServiceProvider::class,
        \Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class
    ];

    /**
     * List of only Local Enviroment Facade Aliases
     * @var array
     */
    protected $facadeAliases = [
        'Debugbar' => \Barryvdh\Debugbar\Facade::class,
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->isLocal()) {
            // to insert cors middleware in debugbar
            // first routing rule wins
            $routeConfig = [
                'namespace' => 'Barryvdh\Debugbar\Controllers',
                'prefix'    => $this->app['config']->get('debugbar.route_prefix'),
            ];

            $this->app['router']->group($routeConfig, function ($router) {
                $router->get('open', [
                    'middleware' => 'cors',
                    'uses'       => 'OpenHandlerController@handle',
                    'as'         => 'debugbar.openhandler',
                ]);
            });
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->registerServiceProviders();
            $this->registerFacadeAliases();
        }
    }

    /**
     * Loda local service providers
     */
    protected function registerServiceProviders()
    {
        foreach ($this->localProviders as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Load additional Aliases
     * Base file Alias load is /config/app.php => aliases
     */
    public function registerFacadeAliases()
    {
        $loader = AliasLoader::getInstance();
        foreach ($this->facadeAliases as $alias => $facade) {
            $loader->alias($alias, $facade);
        }
    }
}
