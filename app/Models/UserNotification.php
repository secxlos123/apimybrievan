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

    public function getSubject($status_eform )
    {
        switch ($this->type) {
            case 'App\Notifications\PengajuanKprNotification':                
                $subjectNotif = 'Pengajuan KPR Baru';
                break;
            case 'App\Notifications\EFormPenugasanDisposisi':                
                $subjectNotif = 'Penugasan Disposisi';
                break;
            case 'App\Notifications\ApproveEFormCustomer':   
                if($status_eform == 'approved'){
                    $subjectNotif = 'Pengajuan KPR Telah Di Setujui';
                }else {
                    $subjectNotif = 'Customer Telah Menyetujui Form KPR';
                }
                break;
            case 'App\Notifications\RejectEFormCustomer':                
                $subjectNotif = 'Pengajuan KPR Telah Di Tolak';
                break;
            case 'App\Notifications\LKNEFormCustomer':                
                $subjectNotif = 'Prakarsa LKN';
                break;
            default:
                $subjectNotif = 'Type undefined';
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

    public function scopedataNotifications($query){
        
        if($query->where('notifications.type','App\Notifications\PengajuanKprNotification') ) {      /* data for roles pinca new form from nasabah eksternal*/
            $query->where('eforms.recommended',false);
            $query->where('eforms.is_approved',false);
            $query->whereNull('ao_id');
            $query->whereNull('ao_name');
            $query->whereNull('ao_position');
        }
        elseif($query->where('notifications.type','App\Notifications\EFormPenugasanDisposisi') ) {      /* data for roles pinca new form from nasabah internal AO*/
            $query->where('eforms.recommended',false);
            $query->where('eforms.is_approved',false);
            $query->whereNotNull('ao_id');
            $query->whereNotNull('ao_name');
            $query->whereNotNull('ao_position');
            
        }
        
        return $query;  
    }
    
    public function getUnreads($branch_id, $role, $pn){
        $query = $this->leftJoin('eforms','notifications.eform_id','=','eforms.id')
                    ->where('eforms.branch_id',$branch_id)
                    ->Where('eforms.ao_id', $pn)
                    ->orderBy('notifications.created_at', 'DESC');
        
        if($role == 'pinca'){
            if ($query->Orwhere('notifications.type','App\Notifications\PengajuanKprNotification')) {
                $query->whereNull('eforms.ao_id');
                $query->unreads();
            }

            if ($query->Orwhere('notifications.type','App\Notifications\LKNEFormCustomer')) {
                $query->leftJoin('visit_reports','eforms.id','=','visit_reports.eform_id');
                $query->whereNotNull('visit_reports.created_at');
                $query->unreads();
            }
               
        }  

        if($role == 'ao'){
            if($query->Orwhere('notifications.type','App\Notifications\EFormPenugasanDisposisi')){         
                $query->whereNotNull('eforms.ao_id');
                $query->whereNotNull('eforms.ao_name');
                $query->whereNotNull('eforms.ao_position');
                $query->unreads();
            }
           
            if ($query->Orwhere('notifications.type','App\Notifications\VerificationDataNasabah')) {    /*is verification*/
                // unverified
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
                /*
                $query->Where('eforms.status_eform', 'Rejected');*/
                $query->unreads();
            }            
        }
        
        if($role == 'staff'){
            $query->whereNull('notifications.created_at');
        }

        if($role == 'collateral-appraisal'){
            $query->whereNull('notifications.created_at');
        }
        
        if($role == 'collateral'){
            $query->whereNull('notifications.created_at');
        }

        $query->select('notifications.id','notifications.type','notifications.notifiable_id','notifications.notifiable_type','notifications.data','notifications.read_at','notifications.created_at','notifications.updated_at','notifications.branch_id','notifications.role_name','notifications.eform_id','eforms.is_approved','eforms.ao_id');

        return $query;
    }
}