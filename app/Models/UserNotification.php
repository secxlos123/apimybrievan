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

    public function getSubject($status_eform ,$ref_number,$user_id)
    {
        $url = env( 'INTERNAL_APP_URL', 'https://mybri.stagingapps.net' ) . 'eform?ref_number=' . $ref_number.'&ids='.$this->eform_id;
        if($user_id){
            $url = 'eform?ids='.$this->eform_id;
        }else {
            $url = $url;
        }
        switch ($this->type) {
            case 'App\Notifications\PengajuanKprNotification':                
                $subjectNotif = [ 'message' => 'Pengajuan KPR Baru',
                                'url'=> $url,
                                ];
                break;
            case 'App\Notifications\EFormPenugasanDisposisi':                
                $subjectNotif = [ 'message' => 'Penugasan Disposisi',
                                'url'=> $url,
                                ];
                break;
            case 'App\Notifications\ApproveEFormCustomer':   
                if($status_eform == 'approved'){
                    $subjectNotif = [ 'message' => 'Pengajuan KPR Telah Di Setujui',
                                'url'=> $url,
                                ];
                }else {
                    $subjectNotif = [ 'message' => 'Customer Telah Menyetujui Form KPR',
                                'url'=> $url,
                                ];
                }
                break;
            case 'App\Notifications\RejectEFormCustomer':                
                $subjectNotif = [ 'message' => 'Pengajuan KPR Telah Di Tolak',
                                'url'=> $url,
                                ];
                break;
            case 'App\Notifications\LKNEFormCustomer':                
                $subjectNotif = [ 'message' => 'Prakarsa LKN',
                                'url'=> $url,
                                ];
                break;
            case 'App\Notifications\VerificationApproveFormNasabah':                
                $subjectNotif = [ 'message' => 'Customer Telah Menyetujui Form KPR',
                                'url'=> $url,
                                ];
                break;
            case 'App\Notifications\VerificationRejectFormNasabah':                
                $subjectNotif = [ 'message' => 'Customer Telah Menolak Form KPR',
                                'url'=> $url,
                                ];
                break;
            case 'App\Notifications\VerificationDataNasabah':                
                $subjectNotif = [ 'message' => 'Verifikasi Pengajuan KPR',
                                'url'=> env( 'MAIN_APP_URL', 'https://mybri.stagingapps.net' ) . 'verification?ref_number=' . $ref_number.'&ids='.$this->eform_id,
                                ];
                break;
            default:
                 $subjectNotif = [ 'message' => 'Type undefined',
                                'url'=> '',
                                ];
                break;
        }

        return $subjectNotif;
    }

    /**
    * Get the notification all.
    *
    * @return void
    */
    public function getNotifications($branch_name){
        return $this->where('role_name',$branch_name)->orderBy('created_at', 'DESC');
    
    }
    
    public function getUnreads($branch_id, $role, $pn , $user_id){
        $query = $this->leftJoin('eforms','notifications.eform_id','=','eforms.id')
                    ->where('eforms.branch_id',@$branch_id)
                    ->Where('eforms.ao_id', @$pn)
                    // ->where('notifications.notifiable_id',@$user_id)
                    ->orderBy('notifications.created_at', 'DESC');
        
        if(@$role == 'pinca'){
            if ($query->Orwhere('notifications.type','App\Notifications\PengajuanKprNotification')) {
                $query->whereNull('eforms.ao_id')->unreads();
            }

            if ($query->Orwhere('notifications.type','App\Notifications\LKNEFormCustomer')) {
                $query->leftJoin('visit_reports','eforms.id','=','visit_reports.eform_id')
                ->whereNotNull('visit_reports.created_at')
                ->unreads();
            }
               
        }  

        if(@$role == 'ao'){
            if($query->Orwhere('notifications.type','App\Notifications\EFormPenugasanDisposisi')){         
                $query->whereNotNull('eforms.ao_id')
                ->whereNotNull('eforms.ao_name')
                ->whereNotNull('eforms.ao_position')
                ->unreads();
            }
           
            if ($query->Orwhere('notifications.type','App\Notifications\ApproveEFormCustomer')) {    /*is is_approved*/
                /*              
                $query->Where('eforms.status_eform', 'approved');
                $query->Where('eforms.recommended', true);
                $query->Where('eforms.is_approved', true);
                $query->whereNotNull('eforms.recommendation');
                $query->whereNotNull('eforms.pinca_name');
                $query->whereNotNull('eforms.pinca_position');
                $query->whereNotNull('eforms.cons');
                $query->whereNotNull('eforms.pros');*/
               
                $query->unreads();
            }
             

            if ($query->Orwhere('notifications.type','App\Notifications\RejectEFormCustomer')) {    /*is rejected*/
                $query->unreads();
            }

            if ($query->Orwhere('notifications.type','App\Notifications\VerificationApproveFormNasabah')) {    /*verifiy app*/
                $query->unreads();
            } 

            if ($query->Orwhere('notifications.type','App\Notifications\VerificationRejectFormNasabah')) {    /*verifiy app*/
                $query->unreads();
            }            
        }
        
        if(@$role == 'customer'){
            $query->where('notifications.notifiable_id',@$user_id);
            
            if ($query->Orwhere('notifications.type','App\Notifications\VerificationDataNasabah')) {               
                $query->unreads();
            }

            if ($query->Orwhere('notifications.type','App\Notifications\ApproveEFormCustomer')) {    /*is is_approved*/
                $query->unreads();
            }
             

            if ($query->Orwhere('notifications.type','App\Notifications\RejectEFormCustomer')) {    /*is rejected*/
                $query->unreads();
            }
        }

        if(@$role == 'staff'){
            $query->whereNull('notifications.created_at');
        }

        if(@$role == 'collateral-appraisal'){
            $query->whereNull('notifications.created_at');
        }
        
        if(@$role == 'collateral'){
            $query->whereNull('notifications.created_at');
        }

        $query->select('notifications.id','notifications.type','notifications.notifiable_id','notifications.notifiable_type','notifications.data','notifications.read_at','notifications.created_at','notifications.updated_at','notifications.branch_id','notifications.role_name','notifications.eform_id','eforms.is_approved','eforms.ao_id', 'eforms.ref_number');

        return $query;
    }
}