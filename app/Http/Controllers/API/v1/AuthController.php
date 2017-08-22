<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Auth\LoginRequest;
use App\Http\Requests\AuthRequest;

use App\Events\Customer\CustomerRegister;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Helpers\Traits\VerifyUser;
use App\Models\User;
use Activation;
use Sentinel;
use JWTAuth;
use DB;

class AuthController extends Controller
{
	use VerifyUser;

	/**
     * The user has been authenticated.
     *
     * @param 	\Illuminate\Http\Request $request
     * @return 	\Illuminate\Http\Response
     */
    public function authenticate(LoginRequest $request)
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
            $additional = [ 'nik' => null ];
            if( $customer = $user->customer_detail ) {
                $additional = [
                    'nik' => $customer->nik
                ];
            }
	        $code = 200; $status = 'success';
	        $response = [
        		'message' => 'Login Sukses',
	        	'data' 	  => [
		    		'token' => 'Bearer ' . $token,
                    'user_id' => $user->id,
		    		'email' => $user->email,
					'first_name' => $user->first_name,
					'last_name'  => $user->last_name,
                    'fullname'   => $user->fullname,
					'image'	 => $user->image,
					'role' => $user->roles->first()->slug,
					'permission' => $user->roles->first()->permissions,
                    'is_register_completed' => $user->is_register_completed
		    	] + $additional,
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
        DB::beginTransaction();
        $user = Sentinel::register( $request->all() );
        $activation = Activation::create( $user );
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
        event( new CustomerRegister( $user, $activation->code ) );

        DB::commit();
        return response()->success( [
            'message' => 'Register Sukses',
            'data' => $data
        ], 201 );
    }

    /**
     * Register new user as members complete form
     *
     * @param AuthRequest $request
     * @return \Illuminate\Http\Response
     */
    public function registerComplete( AuthRequest $request )
    {
        $token = $request->header( 'Authorization' );
        $user = JWTAuth::toUser( str_replace( 'Bearer ', '', $token ) );
        $user->update( $request->only( [ 'first_name', 'last_name', 'phone', 'mobile_phone', 'gender', 'image' ] ) );
        $user->updateCustomerDetail( $request->except( [ 'first_name', 'last_name', 'phone', 'mobile_phone', 'gender', 'image' ] ) );
        $user->refresh();

        return response()->success( [
            'message' => 'Register Komplit Sukses',
            'data' => $user
        ], 201 );
    }

    /**
     * Activate new user as members
     *
     * @param AuthRequest $request
     * @return \Illuminate\Http\Response
     */
    public function activate( AuthRequest $request )
    {
        DB::beginTransaction();
        $user = User::find( $request->user_id );
        Activation::complete( $user, $request->code );

        DB::commit();
        return response()->success( [
            'message' => 'Aktivasi Sukses',
            'data' => []
        ], 201 );
    }
}
