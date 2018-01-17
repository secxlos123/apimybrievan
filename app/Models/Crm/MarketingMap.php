<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class MarketingMap extends Model
{
    protected $fillable = [
      'category',
      'district_name',
      'address',
      'city',
      'longitude',
      'latitude',
      'pot_account',
      'pot_fund',
      'pot_loan',
      'pot_transaction'
    ];

    public function customers()
    {
      return $this->hasMany('App\Models\Crm\CustomerGroup');
    }
}
