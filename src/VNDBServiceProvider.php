<?php

namespace Shikakunhq\VNDBClient;
use Illuminate\Support\ServiceProvider;


class VNDBServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/vndb.php' => config_path('vndb.php')
        ], 'vndb');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Shikakunhq\VNDBClient\VNDBRequest');
    }
}
