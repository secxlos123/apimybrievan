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
        if ($this->detail) {
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
            }
        }

        return false;
    }

    /**
     * Get customer data status.
     *
     * @return bool
     */
    public function getIsVerifiedAttribute()
    {
        return $this->detail ? $this->detail->is_verified : false;
    }

    /**
     * Get personal information of customer.
     *
     * @return bool
     */
    public function getPersonalAttribute()
    {
        $personal_data = [
            'user_id' => $this->detail ? $this->detail->user_id : '',
            'name' => $this->fullname,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'mobile_phone' => $this->mobile_phone,
            'email' => $this->email,
            'nik' => $this->detail ? $this->detail->nik : '',
            'birth_place_id' => $this->detail ? $this->detail->birth_place_id : '',
            'birth_place' => $this->birth_place,
            'birth_date' => $this->detail ? $this->detail->birth_date : '',
            'address' => $this->detail ? $this->detail->address : '',
            'city_id' => $this->detail ? $this->detail->city_id : '',
            'city' => $this->detail ? ($this->detail->city ? $this->detail->city->name : '') : '',
            'citizenship_id' => $this->detail ? ($this->detail->citizenship_id ? $this->detail->citizenship_id['desc1'] : '') : '',
            'citizenship' => $this->detail ? ($this->detail->citizenship_id ? $this->detail->citizenship_id['desc2'] : '') : '',
            'status' => $this->detail ? $this->detail->status : '',
            'address_status' => $this->detail ? $this->detail->address_status : '',
            'mother_name' => $this->detail ? $this->detail->mother_name : '',
            'couple_name' => $this->detail ? $this->detail->couple_name : '',
            'couple_nik' => $this->detail ? $this->detail->couple_nik : '',
            'couple_birth_date' => $this->detail ? $this->detail->couple_birth_date : '',
            'couple_birth_place_id' => $this->detail ? $this->detail->couple_birth_place_id : '',
            'couple_birth_place' => $this->couple_birth_place,
            'couple_identity' => $this->detail ? $this->detail->couple_identity : '',
            'status_id' => $this->detail ? $this->detail->status_id : ''
        ];

        return $personal_data;
    }

    /**
     * Get work information of customer.
     *
     * @return bool
     */
    public function getWorkAttribute()
    {
        return [
            'type_id' => $this->detail ? ($this->detail->job_type_id ? $this->detail->job_type_id['desc1'] : '') : '',
            'type' => $this->detail ? ($this->detail->job_type_id ? $this->detail->job_type_id['desc2'] : '') : '',
            'work_id' => $this->detail ? ($this->detail->job_id ? $this->detail->job_id['desc1'] : '') : '',
            'work' => $this->detail ? ($this->detail->job_id ? $this->detail->job_id['desc2'] : '') : '',
            'company_name' => $this->detail ? $this->detail->company_name : '',
            'work_field_id' => $this->detail ? ($this->detail->job_field_id ? $this->detail->job_field_id['desc1'] : '') : '',
            'work_field' => $this->detail ? ($this->detail->job_field_id ? $this->detail->job_field_id['desc2'] : '') : '',
            'position' => $this->detail ? $this->detail->position : '',
            'work_duration' => $this->detail ? $this->detail->work_duration : '',
            'work_duration_month' => $this->detail ? ($this->detail->work_duration_month ? $this->detail->work_duration_month : '') : '',
            'office_address' => $this->detail ? $this->detail->office_address : ''
        ];
    }

    /**
     * Get financial information of customer.
     *
     * @return bool
     */
    public function getFinancialAttribute()
    {
        return [
            'salary' => $this->detail ? $this->detail->salary : '',
            'other_salary' => $this->detail ? $this->detail->other_salary : '',
            'loan_installment' => $this->detail ? $this->detail->loan_installment : '',
            'dependent_amount' => $this->detail ? $this->detail->dependent_amount : '',
            'salary_couple' => $this->detail ? $this->detail->couple_salary : '',
            'other_salary_couple' => $this->detail ? $this->detail->couple_other_salary : '',
            'loan_installment_couple' => $this->detail ? $this->detail->couple_loan_installment : ''
        ];
    }

    /**
     * Get contact information of customer.
     *
     * @return bool
     */
    public function getContactAttribute()
    {
        return [
            'emergency_contact' => $this->detail ? $this->detail->emergency_contact : '',
            'emergency_relation' => $this->detail ? $this->detail->emergency_relation : '',
            'emergency_name' => $this->detail ? $this->detail->emergency_name : ''
        ];
    }

    /**
     * Get other information of customer.
     *
     * @return bool
     */
    public function getOtherAttribute()
    {
        return [
            'image' => $this->image,
            'identity' => $this->detail ? $this->detail->identity : '',
            'npwp' => $this->detail ? $this->detail->npwp : '',
        ];
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
        if ($this->detail) {
            if( $this->detail->birth_place_city ) {
                return $this->detail->birth_place_city->name;
            }
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
        if ($this->detail) {
            if( $this->detail->couple_birth_place_city ) {
                return $this->detail->couple_birth_place_city->name;
            }
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
            $data[ 'birth_date' ] = date( 'Y-m-d', strtotime( $data[ 'birth_date' ] ) );
            $data[ 'couple_birth_date' ] = date( 'Y-m-d', strtotime( $data[ 'couple_birth_date' ] ) );
            $data[ 'gender' ] = str_replace( 'PEREMPUAN', 'P', $data[ 'gender' ] );
            $data[ 'gender' ] = str_replace( 'LAKI-LAKI', 'L', $data[ 'gender' ] );
            $data[ 'gender' ] = str_replace( 'Perempuan', 'P', $data[ 'gender' ] );
            $data[ 'gender' ] = str_replace( 'Laki-Laki', 'L', $data[ 'gender' ] );
            $data['emergency_contact'] = $data['emergency_mobile_phone'];
            $this->update( array_except( $data, ['emergency_mobile_phone','form_id','email','verify_status', '_method', 'cif_number' ] ) );
        } else if( $data[ 'verify_status' ] == 'verified' ) {
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