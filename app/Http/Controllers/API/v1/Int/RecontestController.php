<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\EForm;
use App\Models\Recontest;
use App\Models\UserNotification;
use App\Models\User;
use App\Models\UserServices;
use DB;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;
use FCM;

class RecontestController extends Controller
{
    public function __construct(User $user, UserServices $userservices, UserNotification $userNotification)
    {
        $this->user = $user;
        $this->userservices = $userservices;
        $this->userNotification = $userNotification;
        $this->userServices = new UserServices;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  integer $eform_id
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store( $eform_id, Request $request )
    {
        try {
            DB::beginTransaction();

            $data = $request->only(["pros", "cons", "source", "income", "income_salary", "income_allowance", "source_income", "couple_salary", "couple_other_salary"]);

            $data['purpose_of_visit'] = "LKN Recontest";
            $data['ao_recommendation'] = $request->input("recommendation");
            $data['ao_recommended'] = $request->input("recommended");

            // Get User Login
            $user_login = \RestwsHc::getUser();

            $eform = EForm::find($eform_id);

            $typeModule = getTypeModule(EForm::class);
            notificationIsRead($eform_id, $typeModule);

            $usersModel = User::FindOrFail($eform->user_id);

            $eform->update(["is_approved" => true, 'status_eform' => 'Approval2']);
            $recontest = $eform->recontest;
            $recontest->update($data);

            if ( $request->mutations ) {
                $recontest->generateArrayData( $request->mutations, 'mutations' );
            }
            $recontest->generateArrayData( $request->recontest, 'documents' );

            $credentials = [
                'data'        => $eform,
                'user'        => $usersModel,
                'request'     => $request,
                'credentials' => $user_login,
                'recontest'   => true,
            ];

            pushNotification($credentials, 'lknEForm');

        } catch (Exception $e) {
            DB::rollback();
            return response()->success( [
                'message' => $e->getMessage(),
            ], 422);
        }

        DB::commit();
        return response()->success( [
            'message' => 'Data LKN Recontest berhasil dikirim',
            'contents' => $recontest
        ], 201 );
    }
}