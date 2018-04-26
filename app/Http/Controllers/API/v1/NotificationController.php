<?php

namespace App\Http\Controllers\API\v1;

use App\Transformers\NotificationTransformer;
use App\Transformers\SummaryNotificationTransformer;
use Illuminate\Http\Request;
use App\Models\UserNotification;
use App\Http\Controllers\Controller;
use Sentinel;
use App\Models\EForm;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationController extends Controller
{

    public function __construct(EForm $eform, User $user, UserNotification $userNotification)
    {
        $this->eform = $eform;
        $this->userNotification = $userNotification;
        $this->user = $user;
    }

    public function index(Request $request)
    {
       //
    }


    public function unread(Request $request)
    {
        $role = ( request()->header( 'role' ) != '' ) ? request()->header( 'role' ) : 0 ;
        $pn = ( request()->header( 'pn' ) != '' ) ? request()->header( 'pn' ) : '' ;
        $branch_id = ( request()->header( 'branchid' ) != '' ) ? request()->header( 'branchid' ) : 0 ;
    	$user_id = ( request()->header( 'userid' ) != '' ) ? request()->header( 'userid' ) : 0 ;
        $ArrGetDataNotification = [];

        if ( ENV('APP_ENV') == 'local' ) {
            $branch_id = substr( $branch_id, -3 );
            $pn = '000' . $pn;
        }

        $getDataNotification = $this->userNotification->getUnreads( $branch_id, $role, $pn, $user_id );

        if ( $getDataNotification ) {
            foreach ( $getDataNotification->get() as $value ) {
                $ArrGetDataNotification[] = [
                    'id' => $value->id,
                    'url' => $value->getSubject($value->is_approved, $value->ref_number,$user_id)['url'],
                    'subject' => $value->getSubject($value->is_approved, $value->ref_number,$user_id)['message'],
                    'subject_external' => $value->getSubject($value->is_approved, $value->ref_number,$user_id)['message_external'],
                    'type' => $value->type,
                    'notifiable_id' => $value->notifiable_id,
                    'notifiable_type' => $value->notifiable_type,
                    'role_name' => $value->role_name,
                    'branch_id' => $value->branch_id,
                    'data' => $value->data,
                    'created_at' => $value->created_at->diffForHumans(),
                    'is_read' => (bool) $value->is_read,
                    'read_at' => $value->read_at,
                ];
            }
        }
    	return  response()->success( [
            'message' => 'Sukses',
            'contents' => $ArrGetDataNotification
        ], 200 );
    }

    public function unreadMobile(Request $request, $type)
    {
        $role = ( request()->header( 'role' ) != '' ) ? request()->header( 'role' ) : 0 ;
        $pn = ( request()->header( 'pn' ) != '' ) ? request()->header( 'pn' ) : '' ;
        $branch_id = ( request()->header( 'branchId' ) != '' ) ? request()->header( 'branchId' ) : '' ;
        $user_id = ( request()->header( 'userId' ) != '' ) ? request()->header( 'userId' ) : 0 ;
        $limit = (empty($request->limit) ? 10 : $request->limit);

        if ($type == "eks") {
            $data = $this->userNotification->getUnreadsMobile(null, $role, null, $user_id, $limit, false);
        } else {
            $user = \RestwsHc::getUser();
            $branchID = substr( $branch_id, -3 );
            $pn = "000" . $pn;
            $data = $this->userNotification->getUnreadsMobile($branchID, $role, $pn, null, $limit, false);
        }
        \Log::info("=====LIST NOTIF MOBILE======");
        \Log::info($data);
        if ( $data ) {
            foreach ($data as $key => $value) {
                $data[$key]['message'] = [
                    // 'title' => $value['data']['message']['title']
                     'body' => $value->getSubject($value->is_approved, $value->ref_number,$user_id)['message']
                ];
                $data[$key]['message_external'] = [
                    // 'title' => $value['data']['message']['title']
                     'body' => $value->getSubject($value->is_approved, $value->ref_number,$user_id)['message_external']
                ];
            }
        }
        return  response()->success( [
            'message' => 'Sukses',
            'contents' => $data
        ], 200 );
    }


    public function read(Request $request,$slug, $type)
    {
        $is_read = ( request()->header( 'is_read' ) != '' ) ? request()->header( 'is_read' ) : NULL ;
        $notification = $this->userNotification
            ->where( 'slug', $slug)->where( 'type_module', $type)->whereNull('read_at')
            ->first();

        if($notification) $notification->markAsRead($is_read);
        return  response()->success( [
            'message' => 'Sukses',
            'contents' => $notification
        ], 200 );
    }

    public function countNotification(Request $request, $type)
    {
        if ($type == "eks") {
            $user   = $request->user();
            $userID = $user->id;

            if ($user->inRole('developer')) {
                $role = "developer";
            } else if ($user->inRole('customer')) {
                $role = "customer";
            }elseif ($user->inRole('others')) {
                $role = "others";
            }elseif ($user->inRole('developer-sales')) {
                $role = "developer-sales";
            }

            $data = $this->userNotification->getUnreadsMobile(null, $role, null, $userID, null, true);
        }else {
            $user     = \RestwsHc::getUser();
            $role     = $user['role'];
            $branchID = substr($user['branch_id'], -3);
            $pn       = "000".$user['pn'];

            $data = $this->userNotification->getUnreadsMobile($branchID, $role, $pn, null, null, true);
        }

        return response()->success([
            'message' => 'Sukses',
            'contents' => [
                'unread_count' => $data,
            ]
        ], 200);
    }
}
