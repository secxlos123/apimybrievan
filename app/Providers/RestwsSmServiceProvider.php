<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Classes\Client\Services\ServiceRestwsHc;
use GuzzleHttp\Client as HttpClient;

class RestwsSmServiceProvider extends ServiceProvider
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
        $this->app->singleton('bri.restwssm', function ($app) {
            return new ServiceRestwsSm(new HttpClient);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['bri.restwssm'];
    }
}
