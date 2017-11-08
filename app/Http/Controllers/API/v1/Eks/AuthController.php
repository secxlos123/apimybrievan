<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\Eks\AuthRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Events\Customer\CustomerRegister;
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
     * Register new user as members
     *
     * @param AuthRequest $request
     * @return \Illuminate\Http\Response
     */
    public function register( AuthRequest $request )
    {
        DB::beginTransaction();
        
        $baseData = $request->all();

        $baseArray = array ('job_type_id' => 'type_id', 'job_type_name' => 'type', 'job_id' => 'work_id', 'job_name' => 'work', 'job_field_id' => 'work_field_id', 'job_field_name' => 'work_field', 'position' => 'position_id', 'position_name' => 'position');

        foreach ($baseArray as $target => $base) {
            if ( isset($baseData[$base]) ) {
                $baseData[$target] = $baseData[$base];
            }
        }

        $user = Sentinel::register( $baseData );
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
            'mobile_phone' => $user->mobile_phone,   
            'role' => $user->roles->first()->slug,
            'permission' => $user->roles->first()->permissions
        ];
        
        if (in_array(env('APP_ENV'), ['local', 'staging'])) {
            event( new CustomerRegister( $user, $activation->code ) );
        }

        DB::commit();
        return response()->success( [
            'message' => 'Register Sukses',
            'contents' => $data
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
        ], 201 );
    }

    /**
     * The user has been authenticated.
     *
     * @param   \App\Http\Requests\API\v1\Eks\AuthRequest $request
     * @return  \Illuminate\Http\Response
     */
    public function store( AuthRequest $request )
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');
        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->error(['message' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->error(['message' => 'could_not_create_token'], 500);
        }

        $code = 401; $status = 'error';
        $response = ['message' => 'Login Gagal'];

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
                'contents'=> [
                    'token' => 'Bearer ' . $token,
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name'  => $user->last_name,
                    'fullname'   => $user->fullname,
                    'image'  => $user->image,
                    'role' => $user->roles->first()->slug,
                    'permission' => $user->roles->first()->permissions,
                    'is_register_simple' => $user->is_register_simple,
                    'is_register_completed' => $user->is_register_completed
                ] + $additional,
            ];
        }

        // all good so return the token
        return response()->{$status}($response, $code);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request )
    {
        $logout = JWTAuth::invalidate();
        return response()->success( [ 'message' => 'Logout Sukses' ] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AuthRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update( AuthRequest $request )
    {
        $token = $request->header( 'Authorization' );
        $user = JWTAuth::toUser( str_replace( 'Bearer ', '', $token ) );
        $user->update( $request->only( [ 'first_name', 'last_name', 'phone', 'mobile_phone', 'image' ] ) );
        $user->updateCustomerDetail( $request->except( [ 'first_name', 'last_name', 'phone', 'mobile_phone', 'image' ] ) );
        $user->refresh();

        return response()->success( [
            'message' => 'Register Komplit Sukses',
            'contents' => $user
        ], 201 );
    }
}
