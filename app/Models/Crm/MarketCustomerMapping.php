<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class MarketCustomerMapping extends Model
{
    protected $fillable = [
      'customer_name',
      'cif',
      'nik',
      'category',
      'market_mapping_id',
      'created_by',
      'creator_name',
      'branch',
      'uker'
    ];

    // public function market()
    // {
    //   return $this->belongsTo('App\Models\Crm\MarketMapping');
    // }
}
