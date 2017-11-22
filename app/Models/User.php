<?php

namespace App\Models;

use Activation;
use App\Jobs\SendPasswordEmail;
use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;
use File;
use Developer;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;

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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'gender_sim'
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
     * Define user avatar image path.
     *
     * @var array
     */
    protected static $image_path = 'uploads/avatars/';

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
     * The relation to user pihak ke -3
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function thirdparty()
    {
        return $this->hasOne( ThirdParty::class );
    }

    /**
     * The relation to user agent developer
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userdeveloper()
    {
        return $this->hasOne( UserDeveloper::class );
    }

    public function favourite()
    {
      return $this->hasOne( Favourite::class );
    }

    /**
     * Get fullname for the user.
     *
     * @return string
     */
    public function getFullnameAttribute()
    {
        if( isset( $this->attributes[ 'first_name' ] ) || isset( $this->attributes[ 'last_name' ] ) ) {
            return ucfirst( $this->attributes[ 'first_name' ] ) . ' ' . ucfirst( $this->attributes[ 'last_name' ] );
        }
    }

    /**
     * Get fullname for the user.
     *
     * @return string
     */
    public function getIsRegisterSimpleAttribute()
    {
        return ! empty( $this->customer_detail );
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
     * Get user gender sim word.
     *
     * @return string
     */
    public function getGenderSimAttribute()
    {
        $value = $this->gender;
        if( $value == 'Laki-laki' ) {
            return 'L';
        } else if( $value == 'Perempuan' ) {
            return 'P';
        } else {
            return '-';
        }
    }

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getIdentityAttribute( $value )
    {
        if( File::exists( 'uploads/users/' . $this->id . '/' . $value ) ) {
            $image = url( 'uploads/users/' . $this->id . '/' . $value );
        } else {
            $image = url( 'img/noimage.jpg' );
        }
        return $image;
    }

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getCoupleIdentityAttribute( $value )
    {
        if( File::exists( 'uploads/users/' . $this->id . '/' . $value ) ) {
            $image = url( 'uploads/users/' . $this->id . '/' . $value );
        } else {
            $image = url( 'img/noimage.jpg' );
        }
        return $image;
    }

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getImageAttribute( $value )
    {
        $image = url( 'img/avatar.jpg' );
        if(  ! empty( $value ) ) {
            if( File::exists( static::$image_path . $value ) ) {
                $image = url( static::$image_path . $value );
            }
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
        if ( !empty( $image ) ) {
            $path = public_path( static::$image_path );
            if ( ! empty( $this->attributes[ 'image' ] ) ) {
                File::delete( $path . $this->attributes[ 'image' ] );
            }

            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move( $path, $filename );
            $this->attributes[ 'image' ] = $filename;
        }
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
     * @param  string   $relation
     * @return \App\Models\User
     */
    protected function createOrUpdate(Request $request, $user, $relation = null)
    {
        if ( ! $user instanceof $this ) {
            $password = str_random(8);
	        \Log::info('================================== Password ============================');
	        \Log::info($password);
            $request->merge(['password' => bcrypt($password)]);
            $user = $this->create($request->all());
            $activation = Activation::create($user);
            Activation::complete($user, $activation->code);
            dispatch(new SendPasswordEmail($user, $password, 'registered'));
        } else {
            $user->update($request->all());
        }

        if ($relation) $user->{$relation}()->updateOrCreate(['user_id' => $user->id], $request->input());
        $user->roles()->sync($request->input('role_id'));
        return $user;
    }

    /**
     * Get Profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\User
     */
    protected function getProfile(Request $request)
    {
        $user = $request->user();
        return $this->getResponse($user);
    }

    /**
     * Get Profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\User
     */
    protected function getResponse($user)
    {
        if ($user->inRole('developer')) {
            return $this->responseDeveloper($user);
        } else if ($user->inRole('customer')) {
            return [];
        }elseif ($user->inRole('others')) {
            return $this->responseThirdparty($user);
        }elseif ($user->inRole('developer-sales')) {
            return $this->responseDeveloperSales($user);
        }else {
            return $this->responseUser($user);
        }
    }

    /**
     * Get Profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\User
     */
    protected function responseUser($user)
    {
        $user->load(['roles', 'detail']);

        return [
            'id'            => $user->id,
            'fullname'      => $user->fullname,
            'first_name'    => $user->first_name,
            'last_name'     => $user->last_name,
            'email'         => $user->email,
            'phone'         => $user->phone,
            'mobile_phone'  => $user->mobile_phone,
            'gender'        => $user->gender,
            'is_actived'    => $user->is_actived,
            'image'         => $user->image,
            'office_id'     => $user->detail ? $user->detail->office_id : null,
            'office_name'   => $user->detail ? $user->detail->office->name : null,
            'nip'           => $user->detail ? $user->detail->nip : null,
            'position'      => $user->detail ? $user->detail->position : null,
            'role_id'       => $user->roles->first()->id,
            'role_name'     => $user->roles->first()->name,
            'role_slug'     => $user->roles->first()->slug,
        ];
    }

    /**
     * Get Profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\User
     */
    protected function responseDeveloper($user)
    {
        $user->load(['developer.city']);
        $developer = $user->developer;

        return [
            'id'            => $user->id,
            'developer_name'=> $user->fullname,
            'company_name'  => $developer->company_name,
            'email'         => $user->email,
            'phone'         => $user->phone,
            'address'       => $developer->address,
            'mobile_phone'  => $user->mobile_phone,
            'image'         => $user->image,
            'city_id'       => $developer->city ? $developer->city->id : '',
            'city_name'     => $developer->city ? $developer->city->name : '',
            'summary'       => $developer->summary,
            'pks_number'    => $developer->pks_number,
            'plafond'       => number_format($developer->plafond),
            'is_actived'    => $user->is_actived,
            'is_approved'   => $developer->is_approved,
            'developer'     => $developer
        ];
    }

    /**
     * Get developer agent
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\User
     */
    protected function responseDeveloperSales($user)
    {
        $developer = $user->userdeveloper;
        $id = $developer->admin_developer_id;
        $query = \DB::table('developers')
                ->join('cities', 'cities.id', '=', 'developers.city_id')
                ->join('users', 'users.id', '=', 'developers.user_id')
                ->select('developers.company_name', 'developers.address', 'users.mobile_phone', 'cities.name as city_name')
                ->where('user_id', $id)->get();
        foreach ($query as $key => $value) {
        return [
            'id'            => $user->id,
            'name'          => $user->fullname,
            'email'         => $user->email,
            'mobile_phone'  => $user->mobile_phone,
            'birth_date'    => $developer->birth_date,
            'join_date'     => $developer->join_date,
            'admin_developer_id' => $developer->admin_developer_id,
            'company_name'  => $value->company_name,
            'address'       => $value->address,
            'phone_number'  => $value->mobile_phone,
            'city_name'     => $value->city_name,
        ];
         }
    }

    /**
     * Get Profile.Pihak ke -3
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\User
     */
    protected function responseThirdparty($user)
    {
        $user->load(['thirdparty.city']);
        $thirdparty = $user->thirdparty;

        return [
            'id'            => $user->id,
            'name'          => $user->fullname,
            'email'         => $user->email,
            'phone_number'  => $thirdparty->phone_number,
            'address'       => $thirdparty->address,
            'city_id'       => $thirdparty->city->id,
            'city_name'     => $thirdparty->city->name,
            'is_actived'    => $user->is_actived
        ];
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

        $subQuery = "(select max(id) as eform_id, user_id from ( select c.user_id, eforms.id from eforms
            left join customer_details as c on eforms.user_id = c.user_id
            left join visit_reports as v on eforms.id = v.eform_id
            order by eforms.created_at desc) data group by user_id) as last_eform
        ";

        return $query
            ->leftJoin( 'customer_details', 'users.id', '=', 'customer_details.user_id' )
            ->leftJoin( 'cities as c', 'customer_details.city_id', '=', 'c.id' )
            ->leftJoin( 'cities as bplace', 'customer_details.birth_place_id', '=', 'bplace.id' )
            ->leftJoin( 'cities as cbplace', 'customer_details.couple_birth_place_id', '=', 'cbplace.id' )
            ->leftJoin( \DB::Raw($subQuery), function( $join ) {
                return $join->on('last_eform.user_id', '=', 'users.id');
            })
            ->leftJoin( 'eforms as e', 'last_eform.eform_id', '=', 'e.id' )
            ->leftJoin( 'visit_reports as v', 'e.id', '=', 'v.eform_id' )
            ->where( function( $user ) use( $request ) {

                if ($request->has('name')) {
                    $user->whereRaw(
                    "CONCAT(users.first_name, ' ', users.last_name) ilike ?", [ '%' . $request->input( 'name' ) . '%' ]
                );
                }
                if ($request->has('email')) {
                    $user->where( 'users.email', 'ilike', '%' . $request->input( 'email' ) . '%' );
                }

                if( $request->has( 'nik' ) ) {
                    $user->where( 'customer_details.nik', 'ilike', '%' . $request->input( 'nik' ) . '%' );
                }
                if( $request->has( 'user_id' ) ) {
                    $user->where( 'users.id', '=', $request->input( 'user_id' ) );
                }
                if( $request->has( 'city_id' ) ) {
                    $user->where( 'customer_details.city_id', '=', $request->input( 'city_id' ) );
                }
                if( $request->has( 'phone' ) ) {
                    $user->where( 'users.phone', 'ilike', '%' . $request->input( 'phone' ) . '%' );
                }
                if( $request->has( 'gender' ) ) {
                    $user->where( 'users.gender', 'ilike', '%' . $request->input( 'gender' ) . '%' );
                }
                if ($request->has('search')) {
                    $user->whereRaw(
                    "CONCAT(users.first_name, ' ', users.last_name) ilike ?", [ '%' . $request->input( 'search' ) . '%' ]
                    )
                    ->orWhere( 'customer_details.nik', 'ilike', '%' . $request->input( 'search' ) . '%' )
                    ->orWhere('customer_details.city_id', '=', $request->input( 'search' ));
                }

                $user->whereHas( 'roles', function( $role ) { $role->whereSlug( 'customer' ); } );
            } )
            ->select( array_merge( [
                'users.id', 'customer_details.nik', 'customer_details.birth_date', 'customer_details.birth_place_id',
                'customer_details.city_id', 'customer_details.status', 'customer_details.mother_name',
                'customer_details.couple_identity', 'customer_details.couple_nik', 'customer_details.couple_name',
                'customer_details.couple_birth_date', 'customer_details.couple_birth_place_id',
                'customer_details.identity', 'customer_details.address'
                , \DB::Raw("
                    case when e.id is null then 'Tidak Ada Pengajuan'
                    when e.is_approved = false and e.recommended = true then 'Kredit Ditolak'
                    when e.is_approved = true then 'Proses CLF'
                    when v.id is not null then 'Prakarsa'
                    when e.ao_id is not null then 'Disposisii Pengajuan'
                    else 'Pengajuan Kredit' end as application_status
                ")
            ], $userFill ) )->selectRaw( 'c.name AS city, bplace.name AS birth_place, cbplace.name AS couple_birth_place' );
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
                if ($request->has('search')) $query->search($request);
            })
            ->where(function ($user) use ($request) {

                /**
                 * Query for filter user.
                 */
                if ($request->has('office_id')) $user->where('office_id', $request->input('office_id'));
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

    /**
     * Update password.
     *
     * @param \Illuminate\Http\Request $request
     * @return array $return
     */
    public function changePassword(Request $request)
    {
        $return = array(
            'success' => false
            , 'message' => 'Password gagal di ubah.'
        );

        $hasher = \Sentinel::getHasher();

        $oldPassword = $request->old_password;
        $password = $request->password;
        $passwordConf = $request->password_confirmation;

        $user = \Sentinel::getUser();

        if (!$hasher->check($oldPassword, $user->password) || $password != $passwordConf)
        {
             $return['success'] = false;
            $return['message'] = 'Password Lama Tidak Valid';
        }
        else
        {

           \Sentinel::update($user, array('password' => $password));
            $return['success'] = true;
            $return['message'] = 'Password Berhasil di Ubah.';
        }

        return $return;


    }
}
