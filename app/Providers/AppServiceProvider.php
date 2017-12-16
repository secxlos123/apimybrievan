<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \DB::listen(function ($query) {
            \Log::info('--------------------- start query -------------------------');
            \Log::info('query : ' . $query->sql);
            \Log::info('time : ' . $query->time);
            \Log::info('--------------------- end query -------------------------');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
