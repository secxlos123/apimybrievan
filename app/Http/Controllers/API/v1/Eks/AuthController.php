<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\Eks\AuthRequest;
use App\Helpers\Traits\VerifyUser;
use JWTAuth;

class AuthController extends Controller
{
    use VerifyUser;

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
}
