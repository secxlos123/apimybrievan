<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\API\v1\VisitReportRequest;
use App\Models\EForm;
use App\Models\VisitReport;
use DB;
use App\Notifications\LKNEFormCustomer;
use App\Models\UserNotification;
use App\Models\User;
use App\Models\UserServices;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;
use FCM;

class VisitReportController extends Controller
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
     * @param integer $eform_id
     * @param  \App\Http\Requests\API\v1\VisitReportRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store( $eform_id, VisitReportRequest $request )
    {
        DB::beginTransaction();

        \Log::info($request->all());
        $data = $request->all();
    	if (!isset($data['mutations'])){
            $data['mutations'] = array();
        }

        // Get User Login
        $user_login = \RestwsHc::getUser();

        $eform = EForm::find($eform_id);
        
        $typeModule = getTypeModule(EForm::class);
        $notificationIsRead =  $this->userNotification->where( 'slug', $eform_id)->where( 'type_module',$typeModule)
                                       ->whereNull('read_at')
                                       ->first();                
        if($notificationIsRead != NULL){
            $notificationIsRead->markAsRead();
        }
     
        $usersModel = User::FindOrFail($eform->user_id);

        $eform->update([
            'address' => $request->input('address')
            , 'appointment_date' => $request->input('date')
            , 'ao_name' => $user_login['name']
            , 'ao_position' => $user_login['position']
        ]);

        $visit_report = VisitReport::create( [ 'eform_id' => $eform_id ] + $data );
        
        $credentials = [
            'data'        => $eform,
            'user'        => $usersModel,
            'credentials' => $user_login
        ];

        pushNotification($credentials, 'lknEForm');

        DB::commit();
        return response()->success( [
            'message' => 'Data LKN berhasil dikirim',
            'contents' => $visit_report
        ], 201 );
    }
}
