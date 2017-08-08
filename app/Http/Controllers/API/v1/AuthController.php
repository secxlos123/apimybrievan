<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;

use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Helpers\VerifyUser;
use Sentinel;
use JWTAuth;

class AuthController extends Controller
{
	use VerifyUser;

	/**
     * The user has been authenticated.
     *
     * @param 	\Illuminate\Http\Request $request
     * @return 	\Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');
        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->error(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->error(['error' => 'could_not_create_token'], 500);
        }

        $code = 401; $status = 'error';
        $response = ['message' => 'Login Gagal', 'data' => (object) null];

        if ($this->verify($request, $token)) {
	        $user = JWTAuth::toUser($token);
	        $code = 200; $status = 'success';
	        $response = [
        		'message' => 'Login Sukses',
	        	'data' 	  => [
		    		'token' => 'Bearer ' . $token,
		    		'user_id' => $user->id,
		    		'email' => $user->email,
					'first_name' => $user->first_name,
					'last_name'  => $user->last_name,
					'fullname'	 => $user->fullname,
					'role' => $user->roles->first()->slug,
					'permission' => $user->roles->first()->permissions,
		    	],
	        ];
        }

        // all good so return the token
        return response()->{$status}($response, $code);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
    	$logout = JWTAuth::invalidate();
        return response()->success(['message' => 'Logout Sukses', 'data' => (object) null]);
    }

    /**
     * Register new user as members
     *
     * @param AuthRequest $request
     * @return \Illuminate\Http\Response
     */
    public function register( AuthRequest $request )
    {
        $user = Sentinel::register( $request->all() );
        $role = Sentinel::findRoleBySlug( 'customer' );
        $role->users()->attach( $user );
        $token = JWTAuth::fromUser( $user );
        $data = [
            'token' => 'Bearer ' . $token,
            'user_id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'fullname'   => $user->fullname,
            'role' => $user->roles->first()->slug,
            'permission' => $user->roles->first()->permissions
        ];

        return response()->success( [
            'message' => 'Register Sukses',
            'data' => $data
        ] );
    }
}
