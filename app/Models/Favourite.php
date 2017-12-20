<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;


class Favourite extends Model implements AuditableContract
{
    use Auditable;
    /**
     * The fillable columns
     * @var [type]
     */
    protected $fillable = ['user_id', 'property_id', 'is_like'];

    /**
     * Cash columns for spesific type
     * @var [type]
     */
    protected $cash = [
      'is_like' => 'boolean'
    ];
}
