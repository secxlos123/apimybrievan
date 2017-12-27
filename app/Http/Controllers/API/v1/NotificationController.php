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
        $branch_id = request()->header( 'branch_id' );
        $ArrGetDataNotification = [];
        if($branch_id !=  ''){
            $checkRoles = checkRolesInternal($branch_id);
            if(isset($checkRoles)){
                $getDataNotification = $this->userNotification->getNotifications($checkRoles['role'])->get();
                if($getDataNotification){
                    foreach ($getDataNotification as $value) {
                        $ArrGetDataNotification[] = [
                                                'id' => $value->id,
                                                'subject' => $value->getSubject(),
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
            }
        }
        return  response()->success( [
            'message' => 'Sukses',
            'contents' => $ArrGetDataNotification
        ], 200 );
    }

   
    public function unread(Request $request)
    {
        $branch_id = request()->header( 'branch_id' );
        $role = request()->header( 'role' );
    	$pn = request()->header( 'pn' );
        $ArrGetDataNotification = [];
        if($branch_id !=  ''){
            $getDataNotification = $this->userNotification->getUnreads( substr($branch_id,-3), $role, '000'.$pn )->get();
            if($getDataNotification){
                foreach ($getDataNotification as $value) {
                    $ArrGetDataNotification[] = [
                                            'id' => $value->id,
                                            'subject' => $value->getSubject(),
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
