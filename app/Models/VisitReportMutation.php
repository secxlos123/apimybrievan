<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitReportMutation extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'visit_report_mutations';

    /**
     * Disabling timestamp feature.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'visit_report_id', 'date', 'amount', 'type', 'information' ];
}
