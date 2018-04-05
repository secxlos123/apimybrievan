<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Developer extends Model implements AuditableContract
{
    use Auditable;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'city_id', 'company_name', 'address', 'summary', 'pks_description',
        'created_by', 'approved_by', 'is_approved', 'pks_number', 'plafond', 'dev_id_bri'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'user' ];

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
     * Get parent user of user detail.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo( City::class, 'city_id' );
    }

    /**
     * The relation to properties.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany( Property::class );
    }

    /**
     * Get all of the property items for the property.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function propertyTypes()
    {
        return $this->hasManyThrough( PropertyType::class, Property::class );
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
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['dev_id', 'asc'];
        $project = $request->input('project') ? explode('|', $request->input('project')) : ['0', '50'];

        return $query->from('developers_view_table')
            ->where(function ($developer) use (&$request, &$query) {

                /**
                 * Query for search developers.
                 */
                if ($request->has('search')) $query->search($request);
            })
            ->where(function ($developer) use ($request, $project) {

                /**
                 * Query for filter by city_id.
                 */
                if ($request->has('city_id')) $developer->where('city_id', $request->input('city_id'));

                /**
                 * Query for for without independent
                 */
                if ($request->has('without_independent')) {
                    if ($request->without_independent) {
                        $developer->where('bri', '!=', '1');
                    }
                }
                /**
                 * Query for for limit project
                 */
                if ($request->has('project')) $developer->whereBetween('project', $project);

            })
            ->select('*')
            ->selectRaw('(select users.image from users where users.id = developers_view_table.dev_id) as image')
            ->orderBy($sort[0], $sort[1]);
    }

    /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetListsDeveloper($query, Request $request)
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['dev_id', 'asc'];
        $project = $request->input('project') ? explode('|', $request->input('project')) : ['0', '50'];

        return $query->from('developers_view_table')
            ->where(function ($developer) use (&$request, &$query) {

                /**
                 * Query for search developers.
                 */
                if ($request->has('search')) $query->search($request);
            })
            ->where(function ($developer) use ($request, $project) {

                /**
                 * Query for filter by city_id.
                 */
                if ($request->has('city_id')) $developer->where('city_id', $request->input('city_id'));

                /**
                 * Query for for without independent
                 */
                if ($request->has('without_independent')) {
                    if ($request->without_independent) {
                        $developer->where('bri', '!=', '1');
                    }
                }
                /**
                 * Query for for limit project
                 */
                if ($request->has('project')) $developer->whereBetween('project', $project);

            })
            ->select('*')
            ->selectRaw('(select users.image from users where users.id = developers_view_table.dev_id) as image')
            ->where('dev_id', '>', 1)
            ->orderBy($sort[0], $sort[1]);
    }

    /**
     * Scope a query for search user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, Request $request)
    {
        return $query
            ->where('company_name', 'ilike', "%{$request->input('search')}%")
            ->orWhere('name', 'ilike', "%{$request->input('search')}%")
            ->orWhere('email', 'ilike', "%{$request->input('search')}%")
            ->orWhere('phone_number', 'ilike', "%{$request->input('search')}%")
            ->orWhere('city_name', 'ilike', "%{$request->input('search')}%");
    }

    public function getListUserProperties($startList = null, $endList = null, $user_id)
    {
        $developer = Developer::select('user_id')->where('user_id', $user_id)->first();
        $filter = false;
        if(!empty($developer)){
            if(!empty($startList) && !empty($endList)){
                $startList = date("01-m-Y",strtotime($startList));
                $endList   = date("t-m-Y", strtotime($endList));

                $dateStart  = \DateTime::createFromFormat('d-m-Y', $startList);
                $startList = $dateStart->format('Y-m-d h:i:s');

                $dateEnd  = \DateTime::createFromFormat('d-m-Y', $endList);
                $endList = $dateEnd->format('Y-m-d h:i:s');

                $filter = true;
            }else if(empty($startList) && !empty($endList)){
                $now        = new \DateTime();
                $startList = $now->format('Y-m-d h:i:s');

                $endList   = date("t-m-Y", strtotime($endList));
                $dateEnd  = \DateTime::createFromFormat('d-m-Y', $endList);
                $endList = $dateEnd->format('Y-m-d h:i:s');

                $filter = true;
            }else if(empty($endList) && !empty($startList)){
                $now      = new \DateTime();
                $endList = $now->format('Y-m-d h:i:s');

                $startList = date("01-m-Y",strtotime($startList));
                $dateStart  = \DateTime::createFromFormat('d-m-Y', $startList);
                $startList = $dateStart->format('Y-m-d h:i:s');

                $filter = true;
            }
        }

        $data = DB::table('user_developers')
                        ->select('users.first_name',
                                 'users.last_name',
                                 'user_developers.user_id',
                                 DB::raw("COUNT(eforms.id) as eform"),
                                 DB::raw("COUNT(CASE WHEN eforms.status_eform = 'Approval1' THEN 1 END) AS eform_approved"))
                        ->leftjoin("developers", 'developers.id', 'user_developers.admin_developer_id')
                        ->leftjoin("users", 'users.id', 'user_developers.user_id')
                        ->leftJoin("eforms", 'eforms.sales_dev_id', 'user_developers.user_id')
                        ->leftJoin("kpr", 'kpr.eform_id', 'eforms.id')
                        ->where('user_developers.admin_developer_id', $developer['user_id'])
                        ->when($filter, function($query) use ($startList, $endList){
                            return $query->whereBetween('eforms.created_at', [$startList, $endList]);
                        })
                        ->groupBy('first_name', 'last_name', 'user_developers.user_id')
                        ->get();

        return $data;
    }
}