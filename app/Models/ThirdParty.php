<?php

namespace App\Models;

use App\Models\City;
use Illuminate\Database\Eloquent\Model;

class ThirdParty extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama_perusahaan', 'alamat_perusahaan', 'city_id', 'no_telp', 'email', 'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    /**
     * Get parent user of user detail.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

}
