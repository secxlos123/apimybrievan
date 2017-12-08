<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\User;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Native\Facades\Sentinel;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $_user;

    public function __construct(User $user, Request $request)
    {
        if ( $request->header('_token') ) {
            $user = Sentinel::findByPersistenceCode($request->header('_token'));

            if ( $user ) {
                $user_id = $user->id;

                $userModel = User::findOrFail($user_id);

                $this->_user = $userModel;
            }
        }
    }
}
