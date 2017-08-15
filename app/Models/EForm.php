<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EForm extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'eforms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'internal_id', 'ao_id', 'appointment_date', 'longitude', 'latitude', 'branch', 'product', 'prescreening_status', 'is_approved'
    ];

    /**
     * The relation to user details.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo( Customer::class, 'user_id' );
    }

    /**
     * The relation to user details.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ao()
    {
        return $this->belongsTo( User::class, 'ao_id' );
    }

    /**
     * The relation to user details.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function internal()
    {
        return $this->belongsTo( user::class, 'internal_id' );
    }
}