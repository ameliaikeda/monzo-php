<?php

namespace Amelia\Monzo;

use Amelia\Monzo\Contracts\Client as ClientContract;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Amelia\Monzo\Socialite\MonzoProvider;

/**
 * Laravel 5.4+ service provider.
 *
 * @author Amelia Ikeda <amelia@dorks.io>
 * @license BSD-3-Clause
 */
class MonzoServiceProvider extends ServiceProvider
{
    /**
     * Boot this service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/monzo.php', 'monzo');

        if (class_exists('Laravel\Socialite\SocialiteManager')) {
            \Laravel\Socialite\Facades\Socialite::extend('monzo', function () {
                $config = $this->app['config']['monzo'];

                return new MonzoProvider(
                    $this->app['request'],
                    $config['id'],
                    $config['secret'],
                    $config['redirect']
                );
            });
        }

        $source = __DIR__.'/../migrations/add_monzo_columns.php';
        $destination = database_path('2017_02_19_000000_add_monzo_columns.php');

        $this->publishes([$source => $destination], 'monzo');
    }

    /**
     * Register services with the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ClientContract::class, function (Application $app) {
            $config = $app['config']['monzo'];

            return new Client(new Guzzle, $config['id'], $config['secret']);
        });

        $this->app->singleton(Monzo::class, function (Application $app) {
            return new Monzo($app->make(ClientContract::class));
        });

        $this->app->alias('monzo', Monzo::class);
        $this->app->alias('monzo.client', ClientContract::class);
    }
}
