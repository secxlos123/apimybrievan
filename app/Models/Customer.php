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
    protected $visible = [ 'personal', 'work', 'financial', 'contact', 'other', 'schedule' ];
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ 'personal', 'work', 'financial', 'contact', 'other', 'schedule' ];

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
                'birth_place' => $detail->birth_place,
                'birth_date' => $detail->birth_date,
                'address' => $detail->address,
                'citizenship' => $detail->citizenship,
                'status' => $detail->status,
                'address_status' => $detail->address_status,
                'mother_name' => $detail->mother_name
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
                'type' => $detail->work_type,
                'work' => $detail->work,
                'company_name' => $detail->company_name,
                'work_field' => $detail->work_field,
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
        foreach ( $this->eforms as $key => $eform ) {
            $schedules[] = [
                'date' => $eform->appointment_date,
                'ao_name' => $eform->ao_name,
                'office' => $eform->office_id,
                'agenda' => ''
            ];
        }
        return $schedules;
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public static function create( $data ) {
        $password = str_random( 8 );
        $separate_array_keys = array_flip( [ 'email', 'password', 'permissions', 'last_login', 'first_name', 'last_name', 'image', 'phone', 'mobile_phone', 'gender' ] );
        $user_data = array_intersect_key( $data, $separate_array_keys ) + [ 'password' => $password ];
        $user = Sentinel::registerAndActivate( $user_data );
        $role = Sentinel::findRoleBySlug( 'customer' );
        $role->users()->attach( $user );
        $customer_data = [ 'user_id' => $user->id ] + array_diff_key( $data, $separate_array_keys );
        CustomerDetail::create( $customer_data );

        // send mail notification
        $customer = static::find( $user->id );
        event( new CustomerRegistered( $customer, $password ) );

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
        $separate_array_keys = array_flip( $this->fillable + [ '_method' ] );
        $customer_data = array_diff_key( $attributes, $separate_array_keys );
        $this->detail()->update( $customer_data );

        return true;
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