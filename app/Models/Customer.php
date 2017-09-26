<?php

namespace App\Models;

use App\Events\Customer\CustomerRegistered;
use Illuminate\Database\Eloquent\Builder;
use App\Models\CustomerDetail;
use App\Models\User;
use Sentinel;

class Customer extends User
{
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [ 'is_simple', 'is_completed', 'is_verified', 'personal', 'work', 'financial', 'contact', 'other', 'schedule' ];
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ 'is_simple', 'is_completed', 'is_verified', 'personal', 'work', 'financial', 'contact', 'other', 'schedule' ];

    /**
     * Get information about register simple status.
     *
     * @return string
     */
    public function getIsSimpleAttribute()
    {
        return ! empty( $this->detail );
    }

    /**
     * Get information about register complete status.
     *
     * @return bool
     */
    public function getIsCompletedAttribute()
    {
        $detail = $this->detail->toArray();
        if( $detail[ 'status' ] != 1 ) {
            $detail = array_diff_key( $detail, array_flip( [
                'couple_nik', 'couple_name', 'couple_birth_date', 'couple_birth_place_id', 'couple_identity'
            ] ) );
        }
        $total_data = count( $detail );
        $filled = array_filter( $detail, function( $var ) {
            return $var !== NULL && $var !== '';
        } );
        $total_filled_data = count( $filled );
        if( $total_data - $total_filled_data == 0 ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get customer data status.
     *
     * @return bool
     */
    public function getIsVerifiedAttribute()
    {
        return $this->detail->is_verified;
    }

    /**
     * Get personal information of customer.
     *
     * @return bool
     */
    public function getPersonalAttribute()
    {
        $personal_data = [
            'name' => $this->fullname,
            'gender' => $this->gender,
            'email' => $this->email
        ];
        if( count( $detail = $this->detail ) ) {
            $personal_data += [
                'nik' => $detail->nik,
                'birth_place_id' => $detail->birth_place_id,
                'birth_place' => $this->birth_place,
                'birth_date' => $detail->birth_date,
                'address' => $detail->address,
                'city_id' => $detail->city_id,
                'city' => $detail->city->name,
                'citizenship_id' => $detail->citizenship_id,
                'citizenship' => $detail->citizenship_id,
                'status' => $detail->status,
                'address_status' => $detail->address_status,
                'mother_name' => $detail->mother_name,
                'couple_name' => $detail->couple_name,
                'couple_nik' => $detail->couple_nik,
                'couple_birth_date' => $detail->couple_birth_date,
                'couple_birth_place_id' => $detail->couple_birth_place_id,
                'couple_birth_place' => $this->couple_birth_place,
                'couple_identity' => $detail->couple_identity
            ];
        }

        return $personal_data;
    }

    /**
     * Get work information of customer.
     *
     * @return bool
     */
    public function getWorkAttribute()
    {
        if( count( $detail = $this->detail ) ) {
            return [
                'type_id' => $detail->job_type_id,
                'type' => $detail->job_type_id,
                'work_id' => $detail->job_id,
                'work' => $detail->job_id,
                'company_name' => $detail->company_name,
                'work_field_id' => $detail->job_field_id,
                'work_field' => $detail->job_field_id,
                'position' => $detail->position,
                'work_duration' => $detail->work_duration,
                'office_address' => $detail->office_address
            ];
        }
    }

    /**
     * Get financial information of customer.
     *
     * @return bool
     */
    public function getFinancialAttribute()
    {
        if( count( $detail = $this->detail ) ) {
            return [
                'salary' => $detail->salary,
                'other_salary' => $detail->other_salary,
                'loan_installment' => $detail->loan_installment,
                'dependent_amount' => $detail->dependent_amount
            ];
        }
    }

    /**
     * Get contact information of customer.
     *
     * @return bool
     */
    public function getContactAttribute()
    {
        if( count( $detail = $this->detail ) ) {
            return [
                'phone' => $this->phone,
                'mobile_phone' => $this->mobile_phone,
                'emergency_contact' => $detail->emergency_contact,
                'emergency_relation' => $detail->emergency_relation
            ];
        }
    }

    /**
     * Get other information of customer.
     *
     * @return bool
     */
    public function getOtherAttribute()
    {
        $other_data = [
            'image' => $this->image
        ];
        if( count( $detail = $this->detail ) ) {
            $other_data += [
                'identity' => $detail->identity,
                'npwp' => $detail->npwp,
            ];
        }

        return $other_data;
    }

    /**
     * Get other information of customer.
     *
     * @return bool
     */
    public function getScheduleAttribute()
    {
        $schedules = [];
        $eforms = $this->eforms()->select( [ 'appointment_date', 'ao_id', 'branch_id' ] )->where( 'appointment_date', '>=', date( 'Y-m-d' ) )->get();
        foreach ( $eforms as $key => $eform ) {
            $schedules[] = [
                'date' => $eform->appointment_date,
                'ao_name' => $eform->ao_name,
                'branch' => $eform->branch_id,
                'agenda' => ''
            ];
        }
        return $schedules;
    }

    /**
     * Get customer branch name.
     *
     * @return bool
     */
    public function getBirthPlaceAttribute()
    {
        if( $this->detail->birth_place_city ) {
            return $this->detail->birth_place_city->name;
        }
        return '';
    }

    /**
     * Get customer branch name.
     *
     * @return bool
     */
    public function getCoupleBirthPlaceAttribute()
    {
        if( $this->detail->couple_birth_place_city ) {
            return $this->detail->couple_birth_place_city->name;
        }
        return '';
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public static function create( $data ) {
        $user_model = new User;
        $password = str_random( 8 );
        $separate_array_keys = array_flip( $user_model->fillable );
        $user_data = array_intersect_key( $data, $separate_array_keys ) + [ 'password' => $password ];
        $user = Sentinel::registerAndActivate( $user_data );
        $role = Sentinel::findRoleBySlug( 'customer' );
        $role->users()->attach( $user );
        $customer_data = [ 'user_id' => $user->id ] + array_diff_key( $data, $separate_array_keys );
        CustomerDetail::create( $customer_data );

        // send mail notification
        $customer = static::find( $user->id );
        // event( new CustomerRegistered( $customer, $password ) );

        return $customer;
    }

    /**
     * Update the model in the database.
     *
     * @param  array  $attributes
     * @param  array  $options
     * @return bool
     */
    public function update( array $attributes = [], array $options = [] )
    {
        $separate_array_keys = array_flip( $this->fillable );
        $user_data = array_intersect_key( $attributes, $separate_array_keys );
        parent::update( $user_data );
        $separate_array_keys = array_flip( $this->fillable );
        $customer_data = array_diff_key( $attributes, $separate_array_keys );
        unset( $customer_data[ '_method' ] );
        $this->detail()->update( $customer_data );

        return true;
    }

    /**
     * verify customer data.
     *
     * @return void
     */
    public function verify( $data )
    {
        if( $data[ 'verify_status' ] == 'verify' ) {
            $this->detail()->update( $data );
        }
        if( $data[ 'verify_status' ] == 'verified' ) {
            $this->detail()->update( [
                'is_verified' => true
            ] );
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
                $role->whereSlug( 'customer' );
            } );
        } );
    }



    /**************************************************************************************
     *
     * Relationship functions
     *
     **************************************************************************************/

    /**
     * The directories belongs to broadcasts.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function detail()
    {
        return $this->hasOne( CustomerDetail::class, 'user_id' );
    }

    /**
     * The directories belongs to broadcasts.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function eforms()
    {
        return $this->hasMany( EForm::class, 'user_id' );
    }
}