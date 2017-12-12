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

    /**
    * Get the notification all.
    *
    * @return void
    */
    public function getNotifications($branch_name){
        return $this->where('role_name',$branch_name)->orderBy('created_at', 'DESC');
    
    }

    public function scopedataNotifications($query, $branch_id){
        
        if($query->where('notifications.type','App\Notifications\PengajuanKprNotification')) {
            $query->where('eforms.branch_id',$branch_id );
            $query->where('eforms.recommended',false);
            $query->where('eforms.is_approved',false);
        }
        
        return $query;
  
    }
    
    public function getUnreads($branch_id){
        return $this->unreads()
                    ->leftJoin('eforms','notifications.eform_id','=','eforms.id')
                    ->dataNotifications($branch_id)
                    ->orderBy('notifications.created_at', 'DESC');
    }
}