<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\CustomerDetail;

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
        'user_id', 'internal_id', 'ao_id', 'appointment_date', 'longitude', 'latitude', 'office_id', 'product', 'prescreening_status', 'is_approved'
    ];

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getAoNameAttribute()
    {
        if( $ao = $this->ao ) {
            return $ao->fullname;
        }

        return null;
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating( function( $eform ) {
            $customer_detail = CustomerDetail::whereNik( $eform->nik )->first();
            dd( $eform->nik );
            $eform->user_id = $customer_detail->user_id;
        } );
    }

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