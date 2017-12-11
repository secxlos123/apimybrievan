<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
  /**
   * Fields that can be mass assigned.
   *
   * @var array
   */
   protected $fillable = [
      'status_name'
      ];
   protected $table = 'crm_statuses';

    /**
    * Fields that can be mass assigned.
    *
    * @var array
    */
    protected $hidden = ['created_at','updated_at'];
}
