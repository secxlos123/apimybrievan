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

    public function getSubject()
    {
        switch ($this->type) {
            case 'App\Notifications\PengajuanKprNotification':                
                $subjectNotif = 'Pengajuan KPR Baru';
                break;
            case 'App\Notifications\EFormPenugasanDisposisi':                
                $subjectNotif = 'Penugasan KPR Baru';
                break;
            case 'App\Notifications\ApproveEFormCustomer':                
                $subjectNotif = 'Pengajuan KPR Telah Di Setujui';
                break;
            case 'App\Notifications\RejectEFormCustomer':                
                $subjectNotif = 'Pengajuan KPR Telah Di Tolak';
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
        $query = $this->unreads()
                    ->leftJoin('eforms','notifications.eform_id','=','eforms.id')
                    ->where('eforms.branch_id',$branch_id)
                    ->orderBy('notifications.created_at', 'DESC');
        
        if($role == 'pinca'){
            $query->where('notifications.type','App\Notifications\PengajuanKprNotification');   
            $query->whereNull('ao_id');
            $query->whereNull('ao_name');
            $query->whereNull('ao_position');
        }  

        if($role == 'ao'){
            if($query->where('notifications.type','App\Notifications\EFormPenugasanDisposisi')){         
                $query->Where('eforms.ao_id', $pn);
                $query->whereNotNull('eforms.ao_id');
                $query->whereNotNull('eforms.ao_name');
                $query->whereNotNull('eforms.ao_position');
            }

            if ($query->Orwhere('notifications.type','App\Notifications\ApproveEFormCustomer')) {    /*is approved*/
                $query->Where('eforms.ao_id', $pn);
                $query->Where('eforms.status_eform', 'approved');
                $query->Where('eforms.recommended', true);
                $query->Where('eforms.is_approved', true);
                $query->whereNotNull('eforms.recommendation');
                $query->whereNotNull('eforms.pinca_name');
                $query->whereNotNull('eforms.pinca_position');
                $query->whereNotNull('eforms.cons');
                $query->whereNotNull('eforms.pros');
            }            
        }

        return $query;
    }
}