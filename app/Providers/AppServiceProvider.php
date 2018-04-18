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
        if ( ENV("APP_ENV") == "local" && ENV('IS_SSL') == 0 ) {
            \DB::listen(function ($query) {
                \Log::info("--- start query ---");
                \Log::info("query : " . $query->sql);
                \Log::info("bindings : " . json_encode($query->bindings));
                \Log::info("time : " . $query->time);
                \Log::info("--- end query ---");
            });
        } else {
            \URL::forceScheme('https');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
