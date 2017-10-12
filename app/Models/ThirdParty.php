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
        'name', 'address', 'city_id', 'phone_number', 'email', 'user_id', 'created_by',
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
     * Set Default Primary Key
     * @var string
     */
    protected $primaryKey = 'user_id';

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
     * Get parent user of user detail.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo( User::class, 'user_id' );
    }

    /**
     * Get List Filter Pihak ke-3
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     * @author Akse (erwan.akse@wgs.co.id)
     */
    public function scopeGetLists($query, Request $request)
    {
        $thirdPartyfill = $this->thirdPartyfill();

        return $query->leftJoin('cities', 'third_parties.city_id', '=', 'cities.id')
            ->leftJoin('users', 'third_parties.user_id', '=', 'users.id')
            ->where(function ($thirdparty) use ($request) {

                if ($request->has('name')) {
                    $thirdparty->where('third_parties.name', 'ilike', '%' . $request->input('name') . '%');
                }
                if ($request->has('city_id')) {
                    $thirdparty->where('third_parties.city_id', '=', $request->input('city_id'));
                }
                if ($request->has('address')) {
                    $thirdparty->where('third_parties.address', 'ilike', '%' . $request->input('address') . '%');
                }
                if ($request->has('city_name')) {
                    $thirdparty->where('cities.name', 'ilike', '%' . $request->input('city_name') . '%');
                }
                if ($request->has('phone_number')) {
                    $thirdparty->where('third_parties.phone_number', '=', $request->input('phone_number'));
                }
                if ($request->has('email')) {
                    $thirdparty->where('third_parties.email', 'ilike', '%' . $request->input('email') . '%');
                }
                if ($request->has('search')) {
                    $thirdparty->where('third_parties.name', 'ilike', '%' . $request->input('search') . '%')
                        ->orWhere('third_parties.address', 'ilike', '%' . $request->input('search') . '%')
                        ->orWhere('third_parties.email', 'ilike', '%' . $request->input('search') . '%')
                        ->orWhere('cities.name', 'ilike', '%' . $request->input('search') . '%');
                }

            })->select(array_merge(['users.is_actived'], $thirdPartyfill))->selectRaw('cities.name AS city_name');
    }

    /**
     * Get Detail Pihak ke-3
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  \Illuminate\Http\Request  $id
     * @return \Illuminate\Database\Eloquent\Builder
     * @author Akse (erwan.akse@wgs.co.id)
     */
    public function scopeGetDetail($query, $id)
    {
        $thirdPartyfill = $this->thirdPartyfill();
        

        return $query->leftJoin('cities', 'third_parties.city_id', '=', 'cities.id')
            ->leftJoin('users', 'third_parties.user_id', '=', 'users.id')
            ->where('third_parties.user_id', '=', $id)
            ->select(array_merge(['users.is_actived'],$thirdPartyfill))
            ->selectRaw('cities.name AS city_name');
    }
    
    /**
     * Get Fillable Table
     * @return array third party fillable table
     * @author Akse (erwan.akse@wgs.co.id)
     */
    private function thirdPartyfill()
    {
        $thirdPartyfill = [];
        foreach ($this->fillable as $fillable) {
            $thirdPartyfill[] = "third_parties.{$fillable}";
        }
        return $thirdPartyfill;

    }

    public function scopeUpdateThird($query, Request $request, $id)
    {

    }

}
