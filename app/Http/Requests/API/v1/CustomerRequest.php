<?php

namespace App\Http\Requests\API\v1;

use App\Http\Requests\BaseRequest;

class CustomerRequest extends BaseRequest
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
        switch ( strtolower( $this->method() ) ) {
            case 'post':
                return [
                    'nik' => 'required|numeric|digits:16|unique:customer_details,nik',
                    'first_name' => 'required',
                    'last_name' => '',
                    'email' => 'required|email|unique:users,email',
                    'mobile_phone' => 'required|regex:(08)',
                    'status' => 'required|in:0,1,2',
                    'mother_name' => 'required',
                    'birth_place' => 'required',
                    'birth_date' => 'required|date',
                    'identity' => 'required|image|mimes:jpg,jpeg,png',
                    'couple_nik' => 'required_if:status,1|numeric|digits:16',
                    'couple_name' => 'required_if:status,1',
                    'couple_birth_place' => 'required_if:status,1',
                    'couple_birth_date' => 'required_if:status,1|date',
                    'couple_identity' => 'required_if:status,1|image'
                ];
                break;
            
            case 'put':
                if( $this->segment( 6 ) == 'verify' ) {
                    return [
                        'verify_status' => 'required|in:verify,verified',
                        'first_name' => 'required',
                        'last_name' => '',
                        'gender' => 'required',
                        'birth_place' => 'required',
                        'birth_date' => 'required|date',
                        'phone' => 'required',
                        'mobile_phone' => 'required|regex:(08)',
                        'address' => 'required',
                        'citizenship' => 'required',
                        'status' => 'required|in:0,1,2',
                        'address_status' => 'required',
                        'mother_name' => 'required'
                    ];
                } else {
                    return [
                        'nik' => 'required|numeric|digits:16|unique:customer_details,nik,' . $this->customer . ',user_id',
                        'email' => 'required|email|unique:users,email,' . $this->customer,
                        'first_name' => 'required',
                        'birth_place' => 'required',
                        'birth_date' => 'required|date',
                        'address' => 'required',
                        'gender' => 'required|in:L,P',
                        'city' => 'required',
                        'phone' => 'required|regex:(08)',
                        'citizenship' => 'required',
                        'status' => 'required|in:0,1,2',
                        'couple_nik' => 'required_if:status,1|numeric|digits:16',
                        'couple_name' => 'required_if:status,1',
                        'couple_birth_place' => 'required_if:status,1',
                        'couple_birth_date' => 'required_if:status,1|date',
                        'couple_identity' => 'required_if:status,1|image',
                        'address_status' => 'required',
                        'mother_name' => 'required',
                        'mobile_phone' => 'required|regex:(08)',
                        'emergency_contact' => 'required|regex:(08)',
                        'emergency_relation' => 'required',
                        'work_type' => 'required',
                        'work' => 'required',
                        'company_name' => 'required',
                        'work_field' => 'required',
                        'position' => 'required',
                        'work_duration' => 'required',
                        'office_address' => 'required',
                        'salary' => 'required|numeric',
                        'other_salary' => 'required|numeric',
                        'loan_installment' => 'required',
                        'dependent_amount' => 'required',
                        'legal_document' => 'required|image|mimes:jpg,jpeg,png',
                        'salary_slip' => 'required|image|mimes:jpg,jpeg,png',
                        'identity' => 'image|mimes:jpg,jpeg,png',
                        'image' => 'image|mimes:jpg,jpeg,png',
                        'npwp' => 'required|image|mimes:jpg,jpeg,png',
                        'bank_statement' => 'required|image|mimes:jpg,jpeg,png',
                        'family_card' => 'required|image|mimes:jpg,jpeg,png',
                        'marrital_certificate' => 'required_if:status,0,1|image|mimes:jpg,jpeg,png',
                        'diforce_certificate' => 'required_if:status,2|image|mimes:jpg,jpeg,png'
                    ];
                }
                break;
            
            default:
                return [
                    //
                ];
                break;
        }
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData()
    {
        if( $this->has( 'status' ) & $this->status != '1' ) {
            return $this->except( [ 'couple_nik', 'couple_name', 'couple_birth_place', 'couple_birth_date', 'couple_identity' ] );
        }
        return $this->all();
    }
}
