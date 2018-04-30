<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use File;

class Action_dates extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'id','eform_id','action','execute_at','created_at','updated_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];


    /**
     * The relation to EForm.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function eform()
    {
        return $this->belongsTo( EForm::class, 'eform_id' );
    }

    /**
     * Generate array upload data
     *
     * @return void
     **/
 public function getPersonalAttribute()
    {
		
		'id','eform_id','action','execute_at','created_at','updated_at'
        $agingdata = [
            'id' => $this->id,
            'eform_id' => $this->eform_id,
            'action' => str_replace('eform-','',$this->action),
            'execute_at' => $this->execute_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        return $personal_data;
    }
	}
