<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
    'ref_id',
    'nik',
    'name',
    'phone',
    'address',
    'product_type',
    'officer_ref',
    'status',
    'created_by',
    ];
}
