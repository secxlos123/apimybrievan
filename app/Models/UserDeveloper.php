<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class UserDeveloper extends Model implements AuditableContract
{
    use Auditable;
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
        'user_id', 'birth_date', 'join_date', 'admin_developer_id', 'bound_project'
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
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetLists($query, Request $request)
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];

        return $query
                ->from('agen_developers_view_table')
                ->where(function ($developer) use (&$request, &$query){

                  /**
                   * Query for search developers.
                   */
                  if ($request->has('search')){
                        $developer->where(\DB::raw('LOWER(full_name)'), 'like', '%'.strtolower($request->input('search')).'%')
                        ->orWhere(\DB::raw('LOWER(email)'), 'like', '%'.strtolower($request->input('search')).'%')
                        ->orWhere(\DB::raw('LOWER(mobile_phone)'), 'like', '%'.strtolower($request->input('search')).'%');
                    }
                })
                ->where(function ($developer) use ($request) {

                 /**
                  * Query for filter by admin developer id.
                  */
                $developer->where('admin_developer_id', $request->user()->id);
                })
                ->orderBy($sort[0], $sort[1]);

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
}
