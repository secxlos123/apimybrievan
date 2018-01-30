<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class MarketMapping extends Model
{
  protected $fillable = [
    'category',
    'market_name',
    'province',
    'city',
    'longitude',
    'latitude',
    'pot_account',
    'pot_fund',
    'pot_loan',
    'pot_transaction'
  ];

}
