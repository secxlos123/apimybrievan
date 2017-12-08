<?php

namespace App\Models;

use App\Models\User;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Lang;

class UserNotification extends Model
{

    protected $table = 'notifications';
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
    	'id'	=>	'string',
        'data' 	=>	'array',
        'type'	=>	'string'
    ];

    public function scopeUnreads($query)
    {
        return $query->whereNull('read_at');
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Get Subject Notification
     * @return String Subject notification
     */
    public function getSubject()
    {
        /*switch ($this->type) {
            case 'App\Notifications\BoostupTransactionNeedApprovalNotification':
                if ( Arr::get($this->data, 'is_wh') ) {
                    // Wholesaler subject
                    $program = Arr::get($this->data, 'program_name');
                    return Lang::get('notification.btransaction.need_approval_wh', [
                        'program_name'  => $program,
                        'link'  => env('FRONTEND_URL')
                    ]);
                } else {
                    // SR Subject
                    $wh = Wholesaler::find(Arr::get($this->data, 'wholesaler_id'));
                    $program = Arr::get($this->data, 'program_name');

                    return Lang::get('notification.btransaction.need_approval_asm', [
                        'program_name'  => $program,
                        'wholesaler'  => @$wh->name
                    ]);
                }
                $subjectNotif = 'Boostup Was Need Approval Notification';
                break;
            case 'App\Notifications\BoostupTransactionWasCreatedNotification':
                $user = Sentinel::findByPersistenceCode(\Request::header('token'));
                $userModel = User::find($user->id);
                $rsm_app = Arr::get($this->data, 'is_approve') ? 1 : 0;

                if ( $userModel->related->id == Arr::get($this->data, 'approve_by') ) {
                    if ( Arr::get($this->data, 'is_wh') ) {
                        return Lang::get('notification.btransaction.was_approved_wh_self', [
                            'program_name'  => $this->data['program_name']
                        ]);
                    }
                    return Lang::get('notification.btransaction.was_approved_self', [
                        'wholesaler' => $this->data['wholesaler_name'],
                        'program_name' => $this->data['program_name']
                    ]);
                } else if ( Arr::get($this->data, 'is_wh') ) {
                    // Wholesaler subject
                    return Lang::choice('notification.btransaction.was_created_wh', $rsm_app ,[
                        'status' => Arr::get($this->data, 'status')
                    ]);
                } else {
                    $is_app = $this->data['dts_type'] == DtsType::SR_PUSH;
                    $wh = Wholesaler::find(Arr::get($this->data, 'wholesaler_id'));
                    if ( $is_app ) {
                        return Lang::choice('notification.btransaction.was_created_sr', $rsm_app ,[
                            'wholesaler' => @$wh->name,
                            'status' => Arr::get($this->data, 'status')
                        ]);
                    } else {
                        if ( $this->data['dts_type'] == DtsType::NSM_PUSH ) {
                            $rsm_app = 0;
                        }
                        return Lang::choice('notification.btransaction.was_created_other', $rsm_app, [
                            'wholesaler' => @$wh->name,
                            'program_name' => $this->data['program_name'],
                            'dts' => $this->data['dts_name']
                        ]);
                    }
                }
                $subjectNotif = 'Boostup Was Created Notification';
                break;
            default:
                $subjectNotif = 'Type undefined';
                break;
        }

        return $subjectNotif;
        */
    }

  

    /**
    * Mark the notification as read.
    *
    * @return void
    */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
        }
    }

    public function getIsReadAttribute()
    {
        return (bool) $this->read_at;
    }

    public function getNotifications($branch_name){
        return $this->where('role_name',$branch_name)->orderBy('created_at', 'DESC');
    }
}