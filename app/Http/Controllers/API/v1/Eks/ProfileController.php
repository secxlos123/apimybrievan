<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TempUser;
use App\Http\Requests\API\v1\Profile\UpdateRequest;
use App\Http\Requests\API\v1\Eks\ChangePasswordRequest;
use App\Models\Customer;
use App\Models\ThirdParty;


class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $user = $request->user();
        if( $user->inRole( 'customer' ) ) {
            $profile = Customer::find( $user->id );
        } else {
            $profile = User::getProfile( $request );
        }

        return response()->success( [
            'contents' => $profile
        ] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request)
    {
        $user = $request->user();
        
        if ($user->inRole('customer'))
        {
            \DB::beginTransaction();
            $customer = $this->storeUpdate($request, $user); 
             CustomerDetail::findOrFail( $user->id );
            $customer->update( $request->except('_token','name','phone','mobile_phone','gender','_jsvalidation','_jsvalidation_validate_all'));
            $user = User::findOrFail($user->id);
            $user->update($request->only('first_name','last_name','phone','mobile_phone','gender'));
            \DB::commit();
            
            if ($user) {
                return response()->success( [
                'message' => 'Data nasabah berhasil dirubah.',
                'contents' => $customer
                ] );
            }
            
        }
        
        if ($user->inRole('other')) {

            \DB::beginTransaction();
            $thirdparty = ThirdParty::findOrFail( $user->id );
            $thirdparty->update( $request->all() );
            \DB::commit();
            
            if ($thirdparty) {
                return response()->success( [
                'message' => 'Data Pihak ke-3 berhasil dirubah.',
                'contents' => $customer
                ] );
            }
            
        }

        if ($user->inRole('developer') || $user->inRole('others')) {
            $request->merge(['user_id' => $user->id]);
            $temp = TempUser::updateOrCreate(['user_id' => $user->id], $request->all());
        }



        return response()->error(['message' => 'Data profile Tidak Dapat Diirubah.']);
    }

    /**
     * Change password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function change_password(ChangePasswordRequest $request)
    {
        $user = $request->user();
        
        $return = $user->changePassword($request);

        if ($return['success']) {
            return response()->success(['message' => $return['message']],200);
        }
        
        return response()->error(['message' => $return['message']],500);

    }
}
