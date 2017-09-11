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
}
