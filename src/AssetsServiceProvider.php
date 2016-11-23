<?php

namespace Panoscape\Assets;

use Illuminate\Support\ServiceProvider;

class AssetsServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/migrations' => database_path('migrations')
        ], 'migrations');

        $this->app->singleton(\Mimey\MimeTypes::class, function ($app) {
            return new \Mimey\MimeTypes;
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}