<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\BotService;

class BotServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(BotService::class, function ($app) {
            return new BotService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
