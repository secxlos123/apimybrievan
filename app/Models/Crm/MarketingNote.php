<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class MarketingNote extends Model
{
    protected $fillable =[
      'marketing_id',
      'pn',
      'pn_name',
      'note'
    ];

    public function marketing()
    {
      return $this->belongsTo('App/Models/Crm/Marketing');
    }
}
