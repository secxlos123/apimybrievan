<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LosMybri extends Model
{
    protected $fillable = [
    	'pn','app_number','status'
    ];

    protected $hidden = [
        'id'
    ];
}
