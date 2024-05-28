<?php

namespace App\Providers;

use App\Observers\job_seekers_offersObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // job_seekers_offers::observe(job_seekers_offersObserver::class);
    }
}
