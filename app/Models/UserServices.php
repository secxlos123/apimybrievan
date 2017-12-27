<?php

namespace App\Models;

use Activation;
use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;

class UserServices extends Authenticatable
{
    use Notifiable;

    protected $table = 'user_services';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pn', 'branch_id', 'hilfm', 'role', 'name', 'tipe_uker', 'htext', 'posisi', 'last_activity','password','created_at', 'updated_at'
    ];

     /**
     * checked Attributes by role and pn.
     *
     * @var data
     */
    public function checkroleAndpn($role, $pn){
        return \DB::table('user_services')->where('role',$role)->where('pn',$pn)->first();

    }

     /**
     * find Attributes by role and pn.
     *
     * @var data
     */
    public function getByBranchId($branch_id){
        return $this->where('branch_id',$branch_id)->first();

    }
}
