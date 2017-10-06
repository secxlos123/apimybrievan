<?php

namespace App\Models;

use App\Models\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ThirdParty extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'address', 'city_id', 'phone_number', 'email', 'is_actived',
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

    /**
     * GetList Third Party Filter
     *
     * @return void
     * @author Akse (erwanakse@wgs.co.id)
     **/
    public function scopeGetLists($query, Request $request)
    {
        $thirdPartyfill = [];
        foreach ($this->fillable as $fillable) {
            $thirdPartyfill[] = "third_parties.{$fillable}";
        }

        return $query->leftJoin('cities', 'third_parties.city_id', '=', 'cities.id')
            ->where(function ($thirdparty) use ($request) {

                if ($request->has('name')) {
                    $thirdparty->where('third_parties.name', 'ilike', '%' . $request->input('name') . '%');
                }
                if ($request->has('city_id')) {
                    $thirdparty->where('third_parties.city_id', '=' , $request->input('city_id'));
                }
                if ($request->has('address')) {
                    $thirdparty->where('third_parties.address', 'ilike', '%' . $request->input('address') . '%');
                }
                if ($request->has('phone_number')) {
                    $thirdparty->where('third_parties.phone_number', 'ilike', '%' . $request->input('phone_number') . '%');
                }
                if ($request->has('email')) {
                    $thirdparty->where('third_parties.email', 'ilike', '%' . $request->input('email') . '%');
                }
                if ($request->has('is_actived')) {
                    $thirdparty->where('third_parties.is_actived', 'ilike', '%' . $request->input('is_actived') . '%');
                }

            })->select(array_merge(['third_parties.id'], $thirdPartyfill))->selectRaw('cities.name AS city_name');
    }

}
