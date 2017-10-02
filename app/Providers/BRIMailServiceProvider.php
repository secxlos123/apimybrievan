<?php

namespace App\Providers;

use App\Classes\Client\Mail\BRITransportManager;
use Illuminate\Mail\MailServiceProvider;

class BRIMailServiceProvider extends MailServiceProvider
{
    /**
     * Register the Swift Transport instance.
     *
     * @return void
     */
    protected function registerSwiftTransport()
    {
        $this->app->singleton('swift.transport', function ($app) {
            return new BRITransportManager($app);
        });
    }
}
