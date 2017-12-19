<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;

class apiPdmToken extends Model
{
    protected $fillable = [
    `access_token`,
    `expires_in`,
    `token_type`,
    `scope`,
    `clientid`,
    `clientsecret`
    ];
}
