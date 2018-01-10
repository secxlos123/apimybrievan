<?php

namespace App\Http\Controllers\API\v1;

use App\Transformers\NotificationTransformer;
use App\Transformers\SummaryNotificationTransformer;
use Illuminate\Http\Request;
use App\Models\UserNotification;
use App\Http\Controllers\Controller;
use Sentinel;
use App\Models\EForm;
use Carbon\Carbon;

class NotificationController extends Controller
{

    public function __construct(EForm $eform, UserNotification $userNotification)
    {
        $this->eform = $eform;
        $this->userNotification = $userNotification;
    }

    public function index(Request $request)
    {
       //
    }

   
    public function unread(Request $request)
    {
        $branch_id = ( request()->header( 'branch_id' ) != '' ) ? request()->header( 'branch_id' ) : 0 ;
        $role = ( request()->header( 'role' ) != '' ) ? request()->header( 'role' ) : 0 ;
        $pn = ( request()->header( 'pn' ) != '' ) ? request()->header( 'pn' ) : '' ;
    	$user_id = ( request()->header( 'user_id' ) != '' ) ? request()->header( 'user_id' ) : 0 ;
        // \Log::info($ArrGetDataNotification);
        // \Log::info($branch_id .' - '.$role.' - '.$pn.' - '.$user_id);
        // die();
        $ArrGetDataNotification = [];
        $getDataNotification = $this->userNotification->getUnreads( substr($branch_id,-3), $role, '000'.$pn , $user_id)->get();
        if($getDataNotification){
            foreach ($getDataNotification as $value) {
                $ArrGetDataNotification[] = [
                                        'id' => $value->id,
                                        'url' => $value->getSubject($value->is_approved, $value->ref_number)['url'],
                                        'subject' => $value->getSubject($value->is_approved, $value->ref_number)['message'],
                                        'type' => $value->type,
                                        'notifiable_id' => $value->notifiable_id,
                                        'notifiable_type' => $value->notifiable_type,
                                        'role_name' => $value->role_name,
                                        'branch_id' => $value->branch_id,
                                        'data' => $value->data,
                                        'created_at' => $value->created_at->diffForHumans(),
                                        'is_read' => (bool) $value->is_read,
                                        'read_at' => Carbon::parse($value->read_at)->format('Y-m-d H:i:s'),
                                    ];
            }
        }
    	return  response()->success( [
            'message' => 'Sukses',
            'contents' => $ArrGetDataNotification
        ], 200 );
    }

    
    public function read($eform_id)
    {
        $notification = $this->userNotification
            ->where('eform_id',$eform_id)
            ->whereNull('read_at')
            ->first();

        if($notification) $notification->markAsRead();        
        return  response()->success( [
            'message' => 'Sukses',
            'contents' => $notification
        ], 200 );

    }
}
