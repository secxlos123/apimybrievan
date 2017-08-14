<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;

use Illuminate\Http\Request;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'permissions', 'last_login', 'first_name', 'last_name', 'birth_place' , 'birth_date', 'address', 'gender', 'city', 'phone', 'citizenship', 'status', 'address_status', 'mother_name', 'mobile_phone', 'emergency_contact', 'emergency_relation', 'identity', 'npwp', 'image', 'work_type', 'work', 'company_name', 'work_field', 'position', 'work_duration', 'office_address', 'salary', 'other_salary', 'loan_installment', 'dependent_amount'
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
        return $query->where( function( $user ) use( $request ) {
            $user->whereRaw( "CONCAT(users.first_name, ' ', users.last_name) ilike ?", [ '%' . $request->input( 'name' ) . '%' ] );
            $user->where( 'email', 'ilike', '%' . $request->input( 'email' ) . '%' );
            $user->where( 'city', 'ilike', '%' . $request->input( 'city' ) . '%' );
            if( $request->has( 'phone' ) ) {
                $user->where( 'phone', 'ilike', '%' . $request->input( 'phone' ) . '%' );
            }
            $user->whereHas( 'roles', function( $role ) { $role->whereSlug( 'customer' ); } );
        } )->select( array_merge( [ 'id' ], $this->fillable ) );
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