<?php

namespace App\Classes\Client\Mail;

use App\Classes\Client\Mail\BRITransport;
use Illuminate\Mail\TransportManager;

class BRITransportManager extends TransportManager
{
	/**
     * Create an instance of the Mailgun Swift Transport driver.
     *
     * @return \App\Classes\Client\Mail\BRITransport
     */
    protected function createBriDriver()
    {
        $config = $this->app['config']->get('services.bri', []);
        $configs = $this->app['config']->get('restapi', []);

        if ( empty($config['domain']) ) {
            $config['domain'] = $configs['restwshc'];
        }

        if ( empty($config['secret']) ) {
            $config['secret'] = $configs['key'];
        }

        return new BRITransport(
            $this->guzzle($config),
            $config['secret'], $config['domain']
        );
    }

}