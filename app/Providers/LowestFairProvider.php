<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\LowestRateService;


class LowestFairProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Library\Services\LowestRateService', function ($app) {
            return new LowestRateService();
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
