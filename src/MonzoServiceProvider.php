<?php

namespace Amelia\Monzo;

use Amelia\Monzo\Socialite\MonzoProvider;
use Illuminate\Support\ServiceProvider;

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
        $this->mergeConfigFrom(__DIR__ . '/../config/monzo.php', 'monzo');

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

        $source = __DIR__ . '/../migrations/add_monzo_columns.php';
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
        //
    }
}
