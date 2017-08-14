<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;

use Illuminate\Http\Request;

use App\Models\CustomerDetail;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'permissions', 'last_login', 'first_name', 'last_name', 'image'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get fullname for the user.
     *
     * @return string
     */
    public function getFullnameAttribute()
    {
        return ucfirst($this->attributes['first_name']) .' '. ucfirst($this->attributes['last_name']);
    }

    /**
     * Find a model by its email.
     *
     * @param  string  $email
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static[]|static|null
     */
    protected function findEmail($email, $columns = ['*'])
    {
        return $this->whereEmail($email)->first($columns);
    }

    /**
     * Scope a query to get lists of roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGetCustomers( $query, Request $request )
    {
        return $query->leftJoin( 'customer_details', 'users.id', '=', 'customer_details.user_id' )->where( function( $user ) use( $request ) {
            $user->whereRaw( "CONCAT(users.first_name, ' ', users.last_name) ilike ?", [ '%' . $request->input( 'name' ) . '%' ] );
            $user->where( 'users.email', 'ilike', '%' . $request->input( 'email' ) . '%' );
            // $user->where( 'customer_details.city', 'ilike', '%' . $request->input( 'city' ) . '%' );
            // if( $request->has( 'phone' ) ) {
            //     $user->where( 'customer_details.phone', 'ilike', '%' . $request->input( 'phone' ) . '%' );
            // }
            $user->whereHas( 'roles', function( $role ) { $role->whereSlug( 'customer' ); } );
        } )->select( array_merge( [ 'users.id' ], $this->fillable ) );
    }

    /**
     * Update customer detail.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function updateCustomerDetail( $input )
    {
        if( $customer_detail = $this->customer_detail ) {
            $customer_detail->update( $input );
        } else {
            CustomerDetail::create( $input + [
                'user_id' => $this->id
            ] );
        }
    }

    /**
     * The directories belongs to broadcasts.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customer_detail()
    {
        return $this->hasOne( CustomerDetail::class );
    }

    /**
     * The directories belongs to broadcasts.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany( Role::class, 'role_users' );
    }
}