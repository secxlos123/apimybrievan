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
            ->where(function ($property) use ($request) {

                if ($request->has('city_id')) $developer->where('prop_city_id', $request->input('city_id'));

            })
            ->select('*')
            ->where('prop_id', '!=', '1')
            ->orderBy($sort[0], $sort[1]);
    }

    /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetDetails($query, $id)
    {

        return $query->from('developer_properties_view_table')
            ->where('prop_id', '=', $id)->get();
    }
}
