<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
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
