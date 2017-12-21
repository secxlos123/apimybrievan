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
        'user_id', 'birth_date', 'join_date', 'admin_developer_id'
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
            ->select(array_merge(['users.is_actived','users.first_name','users.last_name','users.email','users.last_login','users.mobile_phone', 'users.is_banned'],$userdeveloperfill))
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
}
