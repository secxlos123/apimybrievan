<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use JWTAuth;

trait VerifyUser
{
    use AvailableType;

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