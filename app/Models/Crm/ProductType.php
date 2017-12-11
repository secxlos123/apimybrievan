<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
  /**
   * Fields that can be mass assigned.
   *
   * @var array
   */
   protected $fillable = [
      'product_name'
      ];
   protected $table = 'crm_product_types';
    /**
    * Fields that can be mass assigned.
    *
    * @var array
    */
    protected $hidden = ['created_at','updated_at'];
}
