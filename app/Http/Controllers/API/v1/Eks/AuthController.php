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
        $email = strtolower($request->email);
        $request->merge(['email'=>$email]);
        $baseData = $this->reArrangeRequest( $request->all() );

        $user = Sentinel::register( $baseData );
        $user->history()->create(['password'=>$user->password]);
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

        event( new CustomerRegister( $user, $activation->code ) );

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
        $activation = Activation::completed($user);
        \Log::info($activation);
        if ($activation) {
            $check = 0;
        }else{
            Activation::complete( $user, $request->code );
            $check = 1;
        }
        DB::commit();

        if ($check == 1) {
            return response()->success( [
                'message' => 'Aktivasi Sukses',
            ], 201 );
        }else{
            return response()->success( [
                'message' => 'User already activated',
            ], 422 );
        }
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
        $email = strtolower($request->email);
        $request->merge(['email'=>$email]);
        $credentials = $request->only('email', 'password');

        try {
             $check = Sentinel::findByCredentials(['login' => $email]);
             if ($check) {
                if (!$activation = Activation::completed($check)){
                    return response()->error(['message' => 'Maaf akun anda belum di verifikasi'], 401);
                } else if ($check->is_banned || $check->is_actived != TRUE) {
                    return response()->error(['message' => 'Maaf akun anda sedang di banned'], 401);
                } 
             }

            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                    $email_check = User::where('email', $credentials['email'])->get();
                    if(count($email_check) == 0){
                        return response()->error(['message' => 'Alamat Email tersebut belum terdaftar.'], 401);
                    }

                return response()->error(['message' => 'Identitas tersebut tidak cocok dengan data kami.'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->error(['message' => 'could_not_create_token'], 500);
        }

        $code = 401;
        $status = 'error';
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
        } else {
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
        $baseData = $this->reArrangeRequest( $request->except( [ 'first_name', 'last_name', 'phone', 'mobile_phone', 'image', 'gender' ] ) );

        $token = $request->header( 'Authorization' );
        $user = JWTAuth::toUser( str_replace( 'Bearer ', '', $token ) );
        $user->update( $request->only( [ 'first_name', 'last_name', 'phone', 'mobile_phone', 'image', 'gender' ] ) );
        $user->updateCustomerDetail( $baseData );
        $user->refresh();

        return response()->success( [
            'message' => 'Register Komplit Sukses',
            'contents' => $user
        ], 201 );
    }

    /**
     * Update missmatch field name
     *
     * @param Array $request
     * @return Array $request
     */
    public function reArrangeRequest( $request )
    {
        \Log::info($request);

        $baseArray = array (
            'job_type_id' => 'work_type', 'job_type_name' => 'work_type_name'
            , 'job_id' => 'work', 'job_name' => 'work_name'
            , 'job_field_id' => 'work_field', 'job_field_name' => 'work_field_name'
            , 'citizenship_name' => 'citizenship'
        );

        foreach ($baseArray as $target => $base) {
            if ( isset($request[$base]) ) {
                $request[$target] = ( $base == 'email' ? strtolower($request[$base]) : $request[$base] );
                unset($request[$base]);
            }
        }
        \Log::info("==================== re arrange ===================================");
        \Log::info($request);

        return $request;
    }

    /**
     * resend email verification
     *
     * @param AuthRequest $request
     * @return \Illuminate\Http\Response
     */
    public function resendEmail(Request $request)
    {
        $user = User::find( $request->uid );
        $activation = Activation::where('user_id', '=', $request->uid)->first();

        event( new CustomerRegister( $user, $activation->code ) );

        return response()->success( [
            'message' => 'Register Sukses',
            'contents' => $user
        ], 201 );
    }

    /**
     * check version for mobile
     *
     * @param AuthRequest $request
     * @return \Illuminate\Http\Response
     */
    public function versionCheck(Request $request)
    {
        if (isset($request->is_mobile)) {
            if (!$request->mobile_version == env('m_version', 1)) {
                return response()->error(['message' => 'Update APK anda dengan versi terbaru!'], 401);
            }
        }
        return response()->success( [
            'message' => 'APK sudah yang terbaru!',
            'contents' => []
        ], 201 );
    }
}
