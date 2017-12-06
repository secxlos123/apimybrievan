<?php

namespace App\Classes\Client\Services;

use App\Classes\Client\Client;

class ServiceDbwsRest extends Client
{
	/**
     * The headers that will be sent when call the API.
     *
     * @var array
     */
    public function uri()
    {
        $base_url = config('restapi.dbwsrest');

        if (in_array(env('APP_ENV'), ['local', 'staging'])) {
            $this->endpoint = json_decode($this->body['request'])->requestMethod;
            $base_url .= $this->endpoint;
        }
        \Log::info($base_url);
        return $base_url;
    }
}