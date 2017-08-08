<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use JWTAuth;

trait VerifyUser
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

    /**
     * Define params of endpoint.
     *
     * @var string
     */
    protected $params;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
	public function __construct()
	{
		$this->params = request()->segment(3);
	}

	public function verify(Request $request, $token)
	{
		$user = \JWTAuth::toUser($token);
		$role = $user->roles->first()->slug;
		return in_array($role, $this->types[$this->params]);
	}
}