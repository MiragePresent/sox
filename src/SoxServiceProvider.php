<?php

namespace MiragePresent\Sox;

use Illuminate\Support\ServiceProvider;

class SoxServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/sox.php' => config_path('sox.path'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/sox.php', 'sox'
        );
    }
}
