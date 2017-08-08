<?php

namespace App\Helpers;

trait AvailableType
{
	/**
     * Define types of endpoint.
     *
     * @var array
     */
    protected $types = [
        'int' => ['ao', 'mp', 'pinca'],
        'eks' => ['developer', 'customer', 'others']
    ];
}