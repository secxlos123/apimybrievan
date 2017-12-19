<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class rescheduleActivity extends Model
{
  protected $table = 'marketing_reschedule_activities';

  protected $fillable =[
    'activity_id',
    'desc',
    'reason',
    'origin_date',
    'reschedule_date'
  ];

  public function activity()
  {
    return $this->belongsTo('App\Models\Crm\MarketingActivity');
  }
}
