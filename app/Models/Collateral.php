<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Collateral extends Model
{
    





    /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetLists($query, Request $request)
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['prop_id', 'asc'];

        return $query->from('developer_properties_view_table')
            ->where(function ($property) use (&$request, &$query) {

                /**
                 * Query for search developers.
                 */
                if ($request->has('search')) $query->search($request);
            })
            ->where(function ($property) use ($request) {

                /**
                 * Query for filter by city_id.
                 */
                if ($request->has('city_id')) $developer->where('city_id', $request->input('city_id'));

                /**
                 * Query for filter by range project.
                 */
                if ($request->has('project')) $developer->whereBetween('project', explode('|', $request->input('project')));

            })
            ->select('*')
            ->where('prop_id', '!=', '1')
            ->orderBy($sort[0], $sort[1]);
    }
}
