<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {

        // if ( ENV("APP_ENV") == "local" && ENV('IS_SSL') == 0 ) {
        //     \DB::listen(function ($query) {
        //         \Log::info("--- start query ---");
        //         \Log::info("query : " . $query->sql);
        //         \Log::info("bindings : " . json_encode($query->bindings));
        //         \Log::info("time : " . $query->time);
        //         \Log::info("--- end query ---");
        //     });
        // } else if ( ENV("APP_ENV") == "production" && ENV('IS_SSL') == 0 ) {
        //     \DB::listen(function ($query) {
        //         \Log::info("--- start query ---");
        //         \Log::info("query : " . $query->sql);
        //         \Log::info("bindings : " . json_encode($query->bindings));
        //         \Log::info("time : " . $query->time);
        //         \Log::info("--- end query ---");
        //     });
        // } else {
        //     \URL::forceScheme('https');
        // }
        // $host = $request->getSchemeAndHttpHost();
        // $cek = substr($host, 0,8);
        // \Log::info($cek);
        // if( $cek == 'https://' ){
        //     \Log::info("HTTPS");
        //     \URL::forceScheme('https');
        // }

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
