<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Classes\Client\Services\ServiceAsmxLas;
use GuzzleHttp\Client as HttpClient;

class AsmxLasServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('bri.asmx_las', function ($app) {
            return new ServiceAsmxLas(new HttpClient);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['bri.asmx_las'];
    }
}
