<?php

namespace App\Classes\Client\Services;

use App\Classes\Client\Client;

class ServiceRestwsSm extends Client
{
	/**
     * The headers that will be sent when call the API.
     *
     * @var array
     */
    public function uri()
    {
        $base_url = config('restapi.restwssm');

        return $base_url;
    }
}
