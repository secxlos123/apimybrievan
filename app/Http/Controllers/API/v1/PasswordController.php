<?php

namespace App\Http\Controllers\API\v1;

use App\Models\User;
use App\Jobs\SendPasswordEmail;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\Password\ResetRequest;

class PasswordController extends Controller
{
    /**
     * The user send for reset password.
     *
     * @param   \App\Http\Requests\API\v1\Password\ResetRequest $request
     * @return  \Illuminate\Http\Response
     */
    public function reset(ResetRequest $request)
    {
        $user = User::findEmail($request->input('email'));
        $password = str_random(8);
        $user->update(['password' => bcrypt($password)]);
        dispatch(new SendPasswordEmail($user, $password));

        return response()->success([
            'message' => 'Password berhasil direset, silahkan cek email anda untuk mendapatkan password baru',
            'data' => (object) null
        ]);
    }
}
