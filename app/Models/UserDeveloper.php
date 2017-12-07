<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
class UserDeveloper extends Model
{
	/**
     * The table name.
     *`
     * @var string
     */
    protected $table = 'user_developers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'birth_date', 'join_date', 'admin_developer_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'user' ];

    /**
     * Mutator for building_area 
     *
     * @var array
     */

    public function getBuildingAreaAttribute($value) {
        return "Tipe ".$value;
    }
    
    /**
     * Mutator for status is_approved 
     *
     * @var array
     */

    public function getIsApprovedAttribute($value) {
        return ($value) ? 'Approved' : 'Rejected';
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
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetLists($query, Request $request)
    {

        $userdeveloperfill = $this->userdeveloperfill();

        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];

        return $query
            ->leftJoin('users', 'user_developers.user_id', '=', 'users.id')
            ->where(function ($developer) use (&$request, &$query) {

                /**
                 * Query for search developers.
                 */
                if ($request->has('search')) $query->search($request);
            })
            ->where(function ($developer) use ($request) {

                /**
                 * Query for filter by admin developer id.
                 */
                $developer->where('admin_developer_id', $request->user()->id);

            })
            ->select(array_merge(['users.is_actived','users.first_name','users.last_name','users.email','users.last_login','users.mobile_phone'],$userdeveloperfill))
            // ->selectRaw('(select users.image from users where users.id = user_developers.user_id) as image')
            ->orderBy('user_developers'.'.'.$sort[0], $sort[1]);
    }

    /**
     * Get Fillable Table
     * @return array third party fillable table
     * @author Akse (erwan.akse@wgs.co.id)
     */
    private function userdeveloperfill()
    {
        $userdeveloperfill = [];
        foreach ($this->fillable as $fillable) {
            $userdeveloperfill[] = "user_developers.{$fillable}";
        }
        return $userdeveloperfill;

    }

    public function getListUserDeveloper()
    {
        // $data = DB::table('user_developers')
        //                      ->select(
        //                               'users.id',
        //                               'users.first_name', 
        //                               'users.last_name',
        //                               'properties.name',    
        //                               'property_types.price',
        //                               'property_types.building_area',
        //                               'property_items.address',
        //                               'properties.is_approved'
        //                              )
                             // ->join('users', 'user_developers.user_id', '=', 'users.id')
                             // ->join('properties', 'properties.developer_id', '=', 'user_developers.id')
                             // ->join('property_types', 'property_types.property_id', '=', 'properties.id')
                             // ->join('property_items', 'property_items.property_type_id', '=', 'property_types.id')
        //                      ->OrderBy('user_developers.created_at')
        //                      ->groupBy('users.id','users.first_name', 
        //                               'users.last_name',
        //                               'properties.name',    
        //                               'property_types.price',
        //                               'property_types.building_area',
        //                               'property_items.address',
        //                               'properties.is_approved')
        //                      ->limit(5)
        //                      ->get()->toArray();
        $data = UserDeveloper::select('users.first_name', 'users.last_name', 'properties.name', 
                                      'property_items.price', 'property_types.building_area', 
                                      'property_items.address','properties.is_approved')
                             ->join('developers', 'developers.id', '=', 'user_developers.admin_developer_id')
                             ->join('users', 'user_developers.user_id', '=', 'users.id')
                             ->join('properties', 'properties.developer_id', '=', 'user_developers.id')
                             ->join('property_types', 'property_types.property_id', '=', 'properties.id')
                             ->join('property_items', 'property_items.property_type_id', '=', 'property_types.id')
                             ->groupBy('users.id', 'properties.id', 'property_types.id', 'property_items.id')
                             ->get();
        return $data;
    }

    public function getDataChart()
    {

    }
}
