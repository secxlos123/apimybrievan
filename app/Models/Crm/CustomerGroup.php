<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    protected $fillable = [
      'name',
      'nik',
      'cif',
      'category',
      'map_id',
      'created_by',
    ];

    public function district()
    {
      return $this->belongsTo('App\Models\Crm\MarketingMap');
    }
}
