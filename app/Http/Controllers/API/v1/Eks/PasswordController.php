<?php

namespace App\Http\Controllers\API\v1\Eks;

use App\Models\User;
use App\Events\Customer\CustomerReset;
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
        // $password = str_random(8);
        $password = $this->randomPassword(8,"lower_case,upper_case,numbers");
        $user->update(['password' => bcrypt($password)]);
        event(new CustomerReset($user, $password));

        return response()->success([
            'message' => 'Password berhasil direset, silahkan cek email anda untuk mendapatkan password baru'
        ]);
    }

    /**
     * Generate Random Password
     * @param  [type] $length     [description]
     * @param  [type] $characters [description]
     * @return [type]             [description]
     */
    public function randomPassword($length,$characters) {
        // $length - the length of the generated password
        // $characters - types of characters to be used in the password
        // define variables used within the function
        $symbols = array();
        $passwords = array();
        $used_symbols = '';
        $pass = '';
        // an array of different character types
        $symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
        $symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $symbols["numbers"] = '1234567890';
        $symbols["special_symbols"] = '!?~@#-_+<>[]{}';
        $characters = explode(",",$characters); // get characters types to be used for the passsword
        foreach ($characters as $key=>$value) {
            $used_symbols .= $symbols[$value]; // build a string with all characters
        }
        $symbols_length = strlen($used_symbols) - 1; //strlen starts from 0 so to get number of characters deduct 1
            for ($i = 0; $i < $length; $i++) {
                $n = rand(0, $symbols_length); // get a random character from the string with all characters
                $pass .= $used_symbols[$n]; // add the character to the password string
            }

        return $pass; // return the generated password
    }
}
