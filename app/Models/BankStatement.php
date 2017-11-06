<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankStatement extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'bank_statements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'mutation_id', 'date', 'amount', 'type', 'note' ];

    /**
     * Disabling timestamp feature.
     *
     * @var boolean
     */
    public $timestamps = false;

     /**
     * The directories belongs to broadcasts.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mutation()
    {
        return $this->belongsTo( Mutation::class, 'mutation_id' );
    }
}
