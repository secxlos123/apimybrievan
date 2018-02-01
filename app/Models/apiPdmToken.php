<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class apiPdmToken extends Model
{
    protected $fillable = [
    'access_token',
    'expires_in',
    'token_type',
    'scope',
    'clientid',
    'clientsecret'
    ];
}
