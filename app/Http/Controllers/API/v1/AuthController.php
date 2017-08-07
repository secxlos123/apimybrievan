<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * 
     * @return \Illuminate\Http\Response
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
        
        $user = JWTAuth::toUser($token);

        $data = [
    		'token' => 'Bearer ' . $token,
    		'user_id' => $user->id,
    		'email' => $user->email,
			'first_name' => $user->first_name,
			'last_name'  => $user->last_name,
			'fullname'	 => $user->fullname,
			'role' => $user->roles->first()->slug,
			'permission' => $user->roles->first()->permissions,
    	];

        // all good so return the token
        return response()->success([
        	'data' => $data,
        	'message' => 'Login Sukses'
        ]);
    }
}
