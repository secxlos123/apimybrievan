<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class MarketMapping extends Model
{
  protected $fillable = [
    'category',
    'market_name',
    'pos_code',
    'province',
    'city',
    'longitude',
    'latitude',
    'address',
    'pot_account',
    'pot_fund',
    'pot_loan',
    'pot_transaction'
  ];

  // public function customers()
  // {
  //   return $this->hasMany('App\Models\Crm\MarketCustomerMapping');
  // }
}
