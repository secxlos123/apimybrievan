<?php

namespace App\Http\Controllers\API\v1\Eks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ApprovalDataChange;
use App\Http\Requests\API\v1\Profile\CustomerRequest;
use App\Http\Requests\API\v1\Eks\ChangePasswordRequest;
use App\Models\Customer;
use App\Models\CustomerDetail;
use App\Models\ThirdParty;
use App\Models\Developer;
use App\Notifications\EditDeveloper;

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
        \Log::info('===============ini data Profile User======================');
        \Log::info($user);
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
    public function update(CustomerRequest $request)
    {
        $user = $request->user();

        if ($user->inRole('customer'))
        {
            \DB::beginTransaction();
            $profile = User::find( $user->id );

            $profile->update($request->only('image'));

           if ($request->has('name') && $request->input('name') != '') {
               $profile->update($request->only('first_name','last_name','image','gender','phone','mobile_phone'));
            }

            $profile->customer_detail()->updateOrCreate(['user_id'=>$user->id],$request->except('_token', 'name','_method'));
            \DB::commit();

            if ($profile) {
                return response()->success( [
                'message' => 'Data nasabah berhasil diubah.',
                'contents' => $profile
                ],200 );
            }else
            {
                return response()->success( [
                'message' => 'Data nasabah Tidak Dapat diubah.'
                ],422 );
            }

        }

        // if ($user->inRole('other')) {

        //     \DB::beginTransaction();
        //     $thirdparty = ThirdParty::findOrFail( $user->id );
        //     $thirdparty->update( $request->all() );
        //     \DB::commit();

        //     if ($thirdparty) {
        //         return response()->success( [
        //         'message' => 'Data Pihak ke-3 berhasil dirubah.',
        //         'contents' => $thirdparty
        //         ] );
        //     }

        // }

        else if ($user->inRole('developer') || $user->inRole('others')) {
            $type = 'Unknown';
            if ($user->inRole('developer')) {
                $dev = Developer::where(['user_id'=> $user->id])->first();
                $type = \App\Models\Developer::class;

                //$usersModel = User::FindOrFail($user->id);
                //$usersModel->notify(new EditDeveloper($dev));   /*send notification*/

            } elseif ($user->inRole('others')) {
               $type = \App\Models\ThirdParty::class;
               $dev = $user;
            }

            $request->merge(['related_id' => $dev->id,
                              'related_type' => $type,
                              'status' => 'menunggu persetujuan' ]);
            $temp = ApprovalDataChange::updateOrCreate(['related_id' => $dev->id],$request->all());


            if ($temp) {
                return response()->success( [
                'message' => 'Perubahan data sedang dalam proses Modernisasi Staff Bussiness Relations BRI.',
                'contents' => $temp
                ] );
            }
        }



        return response()->error(['message' => 'Data profile Tidak Dapat Diubah.'],422);
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
        else{
            return response()->error(['message' => $return['message']],422);
            }

    }
}
