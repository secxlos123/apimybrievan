<?php

namespace App\Models;

use App\Events\Customer\CustomerRegistered;
use Illuminate\Database\Eloquent\Builder;
use App\Models\CustomerDetail;
use App\Models\User;
use App\Models\EForm;
use Sentinel;
use DB;
class Customer extends User
{
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [ 'is_simple', 'is_completed', 'is_verified', 'personal', 'work', 'financial', 'contact', 'other', 'schedule', 'is_approved', 'is_approved_mobile','IsFinish' ];
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [ 'is_simple', 'is_completed', 'is_verified', 'personal', 'work', 'financial', 'contact', 'other', 'schedule', 'is_approved', 'is_approved_mobile','IsFinish' ];

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
     * Get list lastest customer
     *
     * @return bool
     */

    public function getNewestCustomerAttribute()
    {
        return [
            'name'     => $this->personal['name'],
            'nik'      => $this->personal['nik'],
            'email'    => $this->personal['email'],
            'city'     => $this->personal['city'],
            'phone'    => $this->personal['mobile_phone'],
            'gender'   => $this->personal['gender']
        ];
    }

    /**
     * Get newest chart
     */

    public function getChartAttribute()
    {
        return [
            'month'  => $this->month,
            'month2' => $this->month2,
            'value'  => $this->value,
        ];
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
		if(!empty($this->eforms->IsFinish)){
			$IsFinish = DB::table('eforms')->select("eforms.IsFinish")->where("user_id",$this->detail->user_id)
						->where('id',(\DB::Raw("(select max(id) from eforms where user_id='".$this->detail->user_id."')")))->get()->toArray();
			$IsFinish = json_decode(json_encode($IsFinish), True);
		}else{
			$IsFinish[0]['IsFinish'] = '';
		}
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
            'zip_code' => $this->detail ? ($this->detail->zip_code ? $this->detail->zip_code : '00000') : '00000',
            'kelurahan' => $this->detail ? ($this->detail->kelurahan ? $this->detail->kelurahan : 'kelurahan') : 'kelurahan',
            'kecamatan' => $this->detail ? ($this->detail->kecamatan ? $this->detail->kecamatan : 'kecamatan') : 'kecamatan',
            'kabupaten' => $this->detail ? ($this->detail->kabupaten ? $this->detail->kabupaten : 'kabupaten') : 'kabupaten',
            'current_address' => $this->detail ? $this->detail->current_address : '',
            'zip_code_current' => $this->detail ? ($this->detail->zip_code_current ? $this->detail->zip_code_current : '00000' ) : '00000',
            'kelurahan_current' => $this->detail ? ($this->detail->kelurahan_current ? $this->detail->kelurahan_current : 'kelurahan' ) : 'kelurahan',
            'kecamatan_current' => $this->detail ? ($this->detail->kecamatan_current ? $this->detail->kecamatan_current : 'kecamatan' ) : 'kecamatan',
            'kabupaten_current' => $this->detail ? ($this->detail->kabupaten_current ? $this->detail->kabupaten_current : 'kabupaten' ) : 'kabupaten',
            'city_id' => $this->detail ? $this->detail->city_id : '',
            'city' => $this->detail ? ($this->detail->city ? $this->detail->city->name : '') : '',
            'citizenship_id' => $this->detail ? $this->detail->citizenship_id : '',
            'citizenship' => $this->detail ? $this->detail->citizenship_name : '',
            'status' => $this->detail ? $this->detail->status : '',
            'address_status_id'=> $this->detail ? $this->detail->address_status_id : '',
            'address_status' => $this->detail ? $this->detail->address_status : '',
            'mother_name' => $this->detail ? $this->detail->mother_name : '',
            'couple_name' => $this->detail ? $this->detail->couple_name : '',
            'couple_nik' => $this->detail ? $this->detail->couple_nik : '',
            'couple_birth_date' => $this->detail ? $this->detail->couple_birth_date : '',
            'couple_birth_place_id' => $this->detail ? $this->detail->couple_birth_place_id : '',
            'couple_birth_place' => $this->couple_birth_place,
            'couple_identity' => $this->detail ? $this->detail->couple_identity : '',
            'status_id' => $this->detail ? $this->detail->status_id : '',
            'cif_number'=> $this->detail ? $this->detail->cif_number : '',
            'pendidikan_terakhir' => $this->detail ? $this->detail->pendidikan_terakhir : '',
            'address_domisili' => $this->detail ? $this->detail->address_domisili : '',
            'mobile_phone_couple' => $this->detail ? $this->detail->mobile_phone_couple : '',
            'IsFinish' => $IsFinish[0]['IsFinish'] ? $IsFinish[0]['IsFinish'] : '',
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
            'type_id' => $this->detail ? $this->detail->job_type_id : '',
            'type' => $this->detail ? $this->detail->job_type_name : '',
            'work_id' => $this->detail ? $this->detail->job_id : '',
            'work' => $this->detail ? $this->detail->job_name : '',
            'company_name' => $this->detail ? $this->detail->company_name : '',
            'work_field_id' => $this->detail ? $this->detail->job_field_id : '',
            'work_field' => $this->detail ? $this->detail->job_field_name : '',
            'position_id' => $this->detail ? $this->detail->position : '',
            'position' => $this->detail ? $this->detail->position_name : '',
            'work_duration' => $this->detail ? $this->detail->work_duration : '',
            'work_duration_month' => $this->detail ? $this->detail->work_duration_month : '',
            'office_address' => $this->detail ? $this->detail->office_address : '',
            'zip_code_office' => $this->detail ? ($this->detail->zip_code_office ? $this->detail->zip_code_office : '00000') : '00000',
            'kelurahan_office' => $this->detail ? ($this->detail->kelurahan_office ? $this->detail->kelurahan_office : 'kelurahan') : 'kelurahan',
            'kecamatan_office' => $this->detail ? ($this->detail->kecamatan_office ? $this->detail->kecamatan_office : 'kecamatan') : 'kecamatan',
            'kabupaten_office' => $this->detail ? ($this->detail->kabupaten_office ? $this->detail->kabupaten_office : 'kabupaten') : 'kabupaten'
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
            'status_income' => $this->detail ? ($this->detail->couple_salary == NULL ? 'Pisah Harta':'Gabung Harta') : NULL,
            'status_finance' => $this->detail ? ($this->detail->source_income == NULL || $this->detail->source_income == 'single' ? 'Single Income':'Joint Income') : NULL,
            'salary_couple' => $this->detail ? $this->detail->couple_salary : '',
            'other_salary_couple' => $this->detail ? $this->detail->couple_other_salary : '',
            'loan_installment_couple' => $this->detail ? $this->detail->couple_loan_installment : '',
            'source_income' => $this->detail ? $this->detail->source_income : ''
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
            'family_card' => $this->detail ? $this->detail->family_card : '',
            'marrital_certificate' => $this->detail ? $this->detail->marrital_certificate : '',
            'diforce_certificate' => $this->detail ? $this->detail->diforce_certificate : '',
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
     * Get status is_approved.
     *
     * @return bool
     */

    public function getIsApprovedAttribute()
    {
        $stat_approved = [];
        $eforms = $this->eforms()->select(['is_approved'])->get();
        foreach ($eforms as $eform) {
            $stat_approved = [
                'status' => $eform->is_approved
            ];
        }

        return $stat_approved;
    }

	public function getIsFinishAttribute()
    {
        $stat_approved = [];
        $eforms = $this->eforms()->select(['IsFinish'])->first();
		$stat_approved = $eforms[0]['IsFinish'];
/*         foreach ($eforms as $eform) {
            $stat_approved = [
                'IsFinish' => $eform->IsFinish
            ];
        }
 */
        return $stat_approved;
    }
    /**
     * Get status is_approved for mobile.
     *
     * @return bool
     */

    public function getIsApprovedMobileAttribute()
    {
        $stat_approved = null;
        $eform = $this->eforms()->select(['is_approved', 'IsFinish', 'product_type'])->first();
        if ( $eform ) {
            if ( strtolower($eform->product_type) == "briguna" ) {
                $stat_approved = $eform->IsFinish;

            } else {
                $stat_approved = $eform->is_approved;

            }
        }
        return $stat_approved;
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
        $email = strtolower($data['email']);
        $data['email'] = $email;
        $user_model = new User;
        $password = str_random( 8 );
        $separate_array_keys = array_flip( $user_model->fillable );
        $user_data = array_intersect_key( $data, $separate_array_keys ) + [ 'password' => $password ];
        $user = Sentinel::registerAndActivate( $user_data );
        $user->history()->create(['password' => bcrypt($password) ]);
        $role = Sentinel::findRoleBySlug( 'customer' );
        $role->users()->attach( $user );

        $product = true;
        if (isset($data['product_leads'])) {
            if ($data['product_leads'] != 'kartu_kredit') {
                $product = false;
            }
        }
        if ( $data['status'] == 2 && $product ) {
            $customer_data = [ 'user_id' => $user->id, 'identity'=> $data['identity'], 'couple_identity'=> $data['couple_identity'] ] + array_diff_key( $data, $separate_array_keys );

        } else {
            $customer_data = [ 'user_id' => $user->id, 'identity'=> $data['identity'] ] + array_diff_key( $data, $separate_array_keys );

        }

        CustomerDetail::create( $customer_data );
        // send mail notification
        $customer = static::find( $user->id );
        event( new CustomerRegistered( $customer, $password ,'5') );

        return $customer;
    }


    /**
     * Get the 10 newest customer
     *
     * @return array
     */
    public function newestCustomer()
    {
        $data = Customer::with('detail')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->pluck('newestCustomer');
        return $data;
    }

    /**
     * Get the 10 newest customer
     *
     * @return array
     */
    public function chartNewestCustomer($startChart = null, $endChart = null)
    {
        if(!empty($startChart) && !empty($endChart)){
            $startChart = date("01-m-Y",strtotime($startChart));
            $endChart   = date("t-m-Y", strtotime($endChart));

            $dateStart  = \DateTime::createFromFormat('d-m-Y', $startChart);
            $startChart = $dateStart->format('Y-m-d h:i:s');

            $dateEnd  = \DateTime::createFromFormat('d-m-Y', $endChart);
            $endChart = $dateEnd->format('Y-m-d h:i:s');

            $filter = true;
        }else if(empty($startChart) && !empty($endChart)){
            $now        = new \DateTime();
            $startChart = $now->format('Y-m-d h:i:s');

            $endChart   = date("t-m-Y", strtotime($endChart));
            $dateEnd  = \DateTime::createFromFormat('d-m-Y', $endChart);
            $endChart = $dateEnd->format('Y-m-d h:i:s');

            $filter = true;
        }else if(empty($endChart) && !empty($startChart)){
            $now      = new \DateTime();
            $endChart = $now->format('Y-m-d h:i:s');

            $startChart = date("01-m-Y",strtotime($startChart));
            $dateStart  = \DateTime::createFromFormat('d-m-Y', $startChart);
            $startChart = $dateStart->format('Y-m-d h:i:s');

            $filter = true;
        }else{
            $filter = false;
        }

        $data = Customer::select(
                    DB::raw("count(users.id) as value"),
                    DB::raw("to_char(users.created_at, 'TMMonth YYYY') as month"),
                    DB::raw("to_char(users.created_at, 'MM YYYY') as month2"),
                    DB::raw("to_char(users.created_at, 'YYYY MM') as order")
                )
                ->when($filter, function ($query) use ($startChart, $endChart){
                    return $query->whereBetween('users.created_at', [$startChart, $endChart]);
                })
                ->join('role_users', 'role_users.user_id', '=', 'users.id')
                ->where('role_users.role_id', '5')
                ->groupBy('month', 'month2', 'order')
                ->orderBy("order", "asc")
                ->get()
                ->pluck("chart");

        return $data;
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
        $keys = array('npwp', 'identity', 'couple_identity', 'salary_slip', 'bank_statement', 'family_card', 'marrital_certificate', 'diforce_certificate');

        $separate_array_keys = array_flip( $this->fillable );
        $user_data = array_intersect_key( $attributes, $separate_array_keys );
        parent::update( $user_data );
        $separate_array_keys = array_flip( $this->fillable );
        $customer_data = array_diff_key( $attributes, $separate_array_keys );
        unset( $customer_data[ '_method' ] );
        unset( $customer_data[ 'product_type' ] );
        unset( $customer_data[ 'ao_id' ] );
        if (count($customer_data) > 0) {
          $this->detail()->update( $customer_data );
        }
        if ($this->detail) {
            $this->detail->updateAllImageAttribute( $keys, $customer_data, 'customer' );
        }

        foreach ($keys as $key) {
            if ( isset($data[ $key ]) ) {
                unset( $data[ $key ] );
            }
        }

        return true;
    }

    /**
     * verify customer data.
     *
     * @return void
     */
    public function verify( $data )
    {
        if ( isset($data[ 'eform_id' ]) ) {
            EForm::where( 'id', $data[ 'eform_id' ] )
                ->update([
                    'nik' => $data['nik']
                ]);
            unset($data['eform_id']);
        }

        if( $data[ 'verify_status' ] == 'verify' ) {
            $data[ 'birth_date' ] = date( 'Y-m-d', strtotime( $data[ 'birth_date' ] ) );

            if (isset( $data[ 'couple_birth_date' ] )) {
                $data[ 'couple_birth_date' ] = date( 'Y-m-d', strtotime( $data[ 'couple_birth_date' ] ) );
            }

            $data[ 'gender' ] = str_replace( 'PEREMPUAN', 'P', $data[ 'gender' ] );
            $data[ 'gender' ] = str_replace( 'LAKI-LAKI', 'L', $data[ 'gender' ] );
            $data[ 'gender' ] = str_replace( 'Perempuan', 'P', $data[ 'gender' ] );
            $data[ 'gender' ] = str_replace( 'Laki-Laki', 'L', $data[ 'gender' ] );
            $data['emergency_contact'] = $data['emergency_mobile_phone'];
            $this->update( array_except( $data, ['emergency_mobile_phone','email','verify_status', '_method'] ) );
        } else if( $data[ 'verify_status' ] == 'verified' ) {
            $this->detail()->update( [
                'is_verified' => true
                , 'response_status' => 'approve'
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
        return $this->hasOne( EForm::class, 'user_id' );
    }
}
