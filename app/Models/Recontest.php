<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Recontest extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'eform_id', 'purpose_of_visit', 'pros', 'cons', 'ao_recommendation', 'ao_recommended', 'pinca_recommendation', 'pinca_recommended', 'expired_date', 'documents'
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [ 'documents' => 'array' ];

    /**
     * The relation to EForm.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function eform()
    {
        return $this->belongsTo( EForm::class, 'eform_id' );
    }
}
