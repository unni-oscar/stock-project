<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CurlService;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    // This function registers the CurlService class as a singleton in the app container
   public function register()
    {
        // Register the CurlService class as a singleton in the app container
        $this->app->singleton(CurlService::class, function ($app) {
            // Return a new instance of the CurlService class
            return new CurlService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
