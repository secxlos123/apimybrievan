<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;
use App\Jobs\SendPasswordEmail;
use Illuminate\Http\Request;
use Activation;
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
        return $this->hasOne( UserDetail::class, 'user_id' );
    }

    /**
     * The relation to user details.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function developer()
    {
        return $this->hasOne( Developer::class );
    }

    /**
     * Get fullname for the user.
     *
     * @return string
     */
    public function getFullnameAttribute()
    {
        if( isset( $this->attributes[ 'first_name' ] ) && isset( $this->attributes[ 'last_name' ] ) ) {
            return ucfirst( $this->attributes[ 'first_name' ] ) . ' ' . ucfirst( $this->attributes[ 'last_name' ] );
        }
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
     */
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
     * Created user and attach a role for user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User | array   $user
     * @return \App\Models\User
     */
    protected function createOrUpdate(Request $request, $user)
    {
        if ( ! $user instanceof $this ) {
            $password = str_random(8);
            $request->merge(['password' => bcrypt($password)]);
            $user = $this->create($request->input());
            $activation = Activation::create($user);
            Activation::complete($user, $activation->code);
            dispatch(new SendPasswordEmail($user, $password, 'registered'));
        } else {
            $user->update($request->input());
        }

        $user->roles()->sync($request->input('role_id'));
        return $user;
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
        $userFill = [];
        foreach ($this->fillable as $fillable) {
            $userFill[] = "users.{$fillable}";
        }

        return $query->leftJoin( 'customer_details', 'users.id', '=', 'customer_details.user_id' )->where( function( $user ) use( $request ) {
            $user->whereRaw( "CONCAT(users.first_name, ' ', users.last_name) ilike ?", [ '%' . $request->input( 'name' ) . '%' ] );
            $user->where( 'users.email', 'ilike', '%' . $request->input( 'email' ) . '%' );
            if( $request->has( 'nik' ) ) {
                $user->where( 'customer_details.nik', 'ilike', '%' . $request->input( 'nik' ) . '%' );
            }
            if( $request->has( 'city' ) ) {
                $user->where( 'customer_details.city', 'ilike', '%' . $request->input( 'city' ) . '%' );
            }
            if( $request->has( 'phone' ) ) {
                $user->where( 'users.phone', 'ilike', '%' . $request->input( 'phone' ) . '%' );
            }
            if( $request->has( 'gender' ) ) {
                $user->where( 'users.gender', 'ilike', '%' . $request->input( 'gender' ) . '%' );
            }
            
            $user->whereHas( 'roles', function( $role ) { $role->whereSlug( 'customer' ); } );
        } )->select( array_merge( [ 'users.id', 'customer_details.nik', 'customer_details.city' ], $userFill ) );
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
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];
        
        return $query->from('users_view_table')
            ->where(function ($user) use (&$request, &$query) {

                /**
                 * Query for search user.
                 */
                $query->search($request);
            })
            ->where(function ($user) use ($request) {

                /**
                 * Query for filter user.
                 */
                if ($request->input('office_id')) $user->where('office_id', $request->input('office_id'));
            })
            ->where('id', '!=', $request->user()->id)
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
            ->where('fullname', 'ilike', "%{$request->input('search')}%")
            ->orWhere('email', 'ilike', "%{$request->input('search')}%")
            ->orWhere('mobile_phone', 'ilike', "%{$request->input('search')}%")
            ->orWhere('office_name', 'ilike', "%{$request->input('search')}%")
            ->orWhere('nip', 'ilike', "%{$request->input('search')}%")
            ->orWhere('role_name', 'ilike', "%{$request->input('search')}%")
            ->orWhere('role_slug', 'ilike', "%{$request->input('search')}%");
    }
}