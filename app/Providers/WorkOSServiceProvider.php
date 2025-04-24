<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use WorkOS\WorkOS;

class WorkOSServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WorkOS::class, function ($app) {
            return new WorkOS(config('services.workos.api_key'));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
