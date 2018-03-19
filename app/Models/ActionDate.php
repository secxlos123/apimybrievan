<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionDate extends Model
{
    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['eform_id', 'action', 'execute_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    /**
     * Get parent of eform.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function eform()
    {
        return $this->belongsTo( EForm::class, 'eform_id');
    }
}
