<?php

namespace App\Providers;

use App\Models\VinylMaster;
use App\Observers\VinylMasterObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        VinylMaster::observe(VinylMasterObserver::class);
    }
}
