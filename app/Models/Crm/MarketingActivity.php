<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class MarketingActivity extends Model
{
    protected $fillable = [
      'pn',
      'object_activity',
      'action_activity',
      'start_date',
      'end_date',
      'longitude',
      'latitude',
      'address',
      'marketing_id',
      'pn_join',
      'desc'
    ];
}
