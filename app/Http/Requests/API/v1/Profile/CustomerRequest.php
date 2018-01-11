<?php

namespace App\Http\Requests\API\v1\Profile;

use App\Http\Requests\BaseRequest as FormRequest;
use App\Models\CustomerDetail;
use App\Models\User;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        if( $this->segment( 6 ) == 'personal' )
        {

            $login_session = \Sentinel::getUser();
                    $additional = '';
                    if( $customer_detail = $login_session->customer_detail ) {
                        $additional = ',' . $customer_detail->id;
                    }

        return [
             // 'nik' => 'required|numeric|digits:16|unique:customer_details,nik' . $additional,
             'name' => 'alpha_spaces',
             'birth_place_id' => 'exists:cities,id',
             'birth_date' => 'date',
             // 'address' => 'required',
             // 'city_id' => 'numeric|exists:cities,id',
             'gender' => 'in:L,P',
             // 'citizenship_id' => 'required',
             'status' => 'in:1,2,3',
             'address_status'=>'in:0,1,3',
             // // 'phone' => 'digits:12|numeric',
             'mobile_phone' => 'string|regex:/^[0-9]+$/|min:9|max:12',
             'identity' => 'mimes:jpeg,png,jpg,zip,pdf',
             // 'mother_name'=>''
            ];
        }
        else if ($this->segment( 6 ) == 'work')
        {
             return [
            'job_type_id' => '',
            'job_type_name'=>'',
            'job_id' => '',
            'job_name'=>'',
            'company_name' => '',
            'position' => '',
            'position_name'=>'',
            'job_field_id' => '',
            'job_field_name'=>'',
            'work_duration'=>'',
            'work_duration_month'=>'',
            'office_address'=>''
            ];
        }
        else if ($this->segment( 6 ) == 'avatar')
        {
             return [
            'image' => 'required|image|mimes:jpg,jpeg,png'
            ];
        }
         else if ($this->segment( 6 ) == 'financial')
        {
            return [
            'salary' => '',
            'other_salary' => '',
            'loan_installment' => '',
            'dependent_amount' => '',
            ];

        }
        else if ($this->segment( 6 ) == 'contact')
        {
            return [
            'emergency_contact' => '',
            'emergency_relation' => '',
            'emergency_name' => '',
            ];
        }
        else if ($this->segment( 6 ) == 'other')
        {
            return[
              // 'npwp'=>'required',
              // 'family_card'=>'required'
             // 'couple_identity'=>'required_if:status_id,2',
             // 'marrital_certificate'=>'required_if:status_id,2',
             // 'diforce_certificate'=>'required_if:status_id,3',
             // 'status_id'=>'required'
            ];
        }
        elseif ($this->user()->inRole('developer'))
        {
            return[
             'name' => 'alpha_spaces',
            ];
        }
        elseif ($this->user()->inRole('others'))
        {
            return[
            'name' => 'alpha_spaces',
            'email' => 'email|unique:third_parties,email|max:150',
            'address' => 'string',
            'city_id' => 'integer|exists:cities,id',
            'phone_number' => 'string|regex:/^[0-9]+$/|max:15',

            ];
        }


    }

    /**
     * [messages description]
     * @author erwan.akse@wgs.co.id
     * @return [type] [description]
     */
    public function messages()
    {
        $email = '';
        $nik =  isset($this->nik)?$this->nik:NULL;
        if ($nik != NULL) {
            $detail = CustomerDetail::where('nik','=',$nik)->first();
            if (count($detail) != 0) {
                $user = User::find($detail->user_id);
                $email = $user->email;
            }
        }
        return [
            'nik.unique' => 'Nomor Induk Kartu Penduduk Telah Digunakan Oleh Email '.$email,
        ];
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {

            if ( null !== $this->input('name') ) {
                    list($first_name, $last_name) = name_separator($this->input('name'));
                    $this->merge( compact( 'first_name', 'last_name' ) );
                }

        return parent::getValidatorInstance();
    }

}
