<?php

namespace App\Classes\Client\Services;

use App\Classes\Client\Client;

class ServiceAsmx extends Client
{
	/**
     * The headers that will be sent when call the API.
     *
     * @var array
     */
    public function uri()
    {
        return config('restapi.asmx').$this->endpoint;
    }
}