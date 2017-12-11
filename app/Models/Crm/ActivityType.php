<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
  /**
   * Fields that can be mass assigned.
   *
   * @var array
   */
   protected $fillable = [
      'activity_name'
      ];
   protected $table = 'crm_activity_types';
    /**
    * Fields that can be mass assigned.
    *
    * @var array
    */
    protected $hidden = ['created_at','updated_at'];
}
