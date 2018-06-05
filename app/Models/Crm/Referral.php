<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
    'ref_id',
    'nik',
    'cif',
    'name',
    'phone',
    'address',
    'product_type',
    'officer_ref',
    'status',
    'note',
    'created_by',
    'creator_name',
    'officer_name',
    'branch_id',
    'longitude',
    'latitude',
    'contact_time',
    'intention'
    ];
}
