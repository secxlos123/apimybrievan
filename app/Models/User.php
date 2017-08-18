<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;
use Illuminate\Http\Request;

use App\Models\CustomerDetail;
use File;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'permissions', 'last_login', 'first_name', 'last_name', 'image',
        'phone', 'mobile_phone', 'gender', 'is_actived',
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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_actived' => 'boolean',
    ];

    /**
     * The directories belongs to broadcasts.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customer_detail()
    {
        return $this->hasOne( CustomerDetail::class, 'user_id' );
    }

    /**
     * The relation to user details.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function detail()
    {
        return $this->hasOne( UserDetail::class );
    }

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
     * Get fullname for the user.
     *
     * @return string
     */
    public function getIsRegisterCompletedAttribute()
    {
        return ! empty( $this->customer_detail );
    }

    /**
     * Get user gender hhumanify word.
     *
     * @return string
     */
    public function getGenderAttribute( $value )
    {
        if( $value == 'L' ) {
            return 'Laki-laki';
        } else if( $value == 'P' ) {
            return 'Perempuan';
        } else {
            return '-';
        }
    }

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getImageAttribute( $value )
    {
        if( File::exists( 'uploads/avatars/' . $value ) ) {
            $image = url( 'uploads/avatars/' . $value );
        } else {
            $image = url( 'img/avatar.jpg' );
        }
        return $image;
    }

    /**
     * Set user avatar image.
     *
     * @return void
    public function setImageAttribute( $image )
    {
        $path = public_path( 'uploads/avatars/' );
        if ( ! empty( $this->attributes[ 'image' ] ) ) {
            File::delete( $path . $this->attributes[ 'image' ] );
        }

        $filename = time() . '.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'image' ] = $filename;
    }
     */

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
            if( $request->has( 'city' ) ) {
                $user->where( 'customer_details.city', 'ilike', '%' . $request->input( 'city' ) . '%' );
            }
            if( $request->has( 'phone' ) ) {
                $user->where( 'users.phone', 'ilike', '%' . $request->input( 'phone' ) . '%' );
            }
            $user->whereHas( 'roles', function( $role ) { $role->whereSlug( 'customer' ); } );
        } )->select( array_merge( [ 'users.id', 'customer_details.nik', 'customer_details.city' ], $this->fillable ) );
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
                'nik' => hexdec( uniqid() ),
                'user_id' => $this->id
            ] );
        }
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
        $sort = ['first_name', 'asc'];
        if ($request->input('sort')) $sort = explode('|', $request->input('sort'));

        return $query->with(['detail.office', 'roles'])
            ->where(function ($user) use (&$request, &$query) {
                $query->search($request);
            })
            ->where(function ($user) use ($request) {

                /**
                 * Query for filter user.
                 */
                if ($request->input('office_id')) {
                    $user->whereHas('detail', function ($detail) use ($request) {
                        $detail->where('office_id', $request->input('office_id'));
                    });
                }
            })
            ->orderBy($sort[0], $sort[1])
            ->whereDoesntHave('roles', function ($role) { $role->whereIn('slug', ['developer', 'customer', 'others']); })
            ->select(array_merge(['id'], $this->fillable));
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
        return $query->whereRaw("CONCAT(first_name, last_name) ilike ?", ["%{$request->input('fullname')}%"])
            ->orWhere("email", 'ilike', "%{$request->input('email')}%")
            ->orWhereHas('detail', function ($detail) use ($request) {
                $detail->where('user_details.nip', 'ilike', "%{$request->input('nip')}%")
                    ->orWhereHas('office', function ($office) use ($request) {
                        $office->where('offices.name', 'ilike', "%{$request->input('office_name')}%");
                    });
            })
            ->orWhereHas('roles', function ($roles) use ($request) {
                $roles->where('roles.slug', 'ilike', "%{$request->input('role_slug')}%");
            });
    }
}