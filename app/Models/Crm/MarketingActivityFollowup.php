<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class MarketingActivityFollowup extends Model
{
    protected $table ='marketing_activity_followups';

    protected $fillable = [
      'activity_id',
      'desc',
      'fu_result',
      'count_rekening',
      'amount',
      'target_commitment_date',
      'longitude',
      'latitude'
    ];

    public function activity()
    {
      return $this->belongsTo('App\Models\Crm\MarketingActivity');
    }
}
