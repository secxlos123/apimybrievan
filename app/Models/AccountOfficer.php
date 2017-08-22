<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\User;

class AccountOfficer extends User
{
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */

    protected $visible = [ 'id', 'nip', 'name', 'position', 'email', 'gender', 'image' ];
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ 'nip', 'name', 'position' ];

    /**
     * Get fullname for the user.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->fullname;
    }

    /**
     * Get fullname for the user.
     *
     * @return string
     */
    public function getNipAttribute()
    {
        if( $detail = $this->detail ) {
            return $this->detail->nip;
        }
        return null;
    }

    /**
     * Get fullname for the user.
     *
     * @return string
     */
    public function getPositionAttribute()
    {
        if( $detail = $this->detail ) {
            return $this->detail->position;
        }
        return null;
    }

    /**
     * Scope a query to filter account officer data.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     */
    public function scopeFilter( $query, Request $request )
    {
        if( $request->has( 'name' ) ) {
            $query->whereRaw( "CONCAT(first_name, ' ', last_name) ilike ?", [ '%' . $request->input( 'name' ) . '%' ] );
        }
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope( 'role', function( Builder $builder ) {
            $builder->whereHas( 'roles', function( $role ) {
                $role->whereSlug( 'ao' );
            } );
        } );
    }
}
