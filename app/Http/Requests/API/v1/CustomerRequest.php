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
                    'mobile_phone' => 'required|string|regex:/^[0-9]+$/|min:9|max:16',
                    'status' => 'required|in:0,1,2',
                    'mother_name' => 'required',
                    'birth_place_id' => 'required|numeric|exists:cities,id',
                    'birth_date' => 'required|date',
                    'identity' => 'required|image|mimes:jpg,jpeg,png',
                    'couple_nik' => 'required_if:status,1|numeric|digits:16',
                    'couple_name' => 'required_if:status,1',
                    'couple_birth_place_id' => 'required_if:status,1',
                    'couple_birth_date' => 'required_if:status,1|date',
                    'couple_identity' => 'required_if:status,1|image'
                ];
                break;
            
            case 'put':
                if( $this->segment( 6 ) == 'verify' ) {
                    return [

                        'form_id'=>'required_if:verify_status,verify',
                        'birth_place_id'=>'required_if:verify_status,verify|exists:cities,id',
                        'birth_date'=>'required_if:verify_status,verify',
                        'city_id'=>'required_if:verify_status,verify|exists:cities,id',
                        'status'=>'required_if:verify_status,verify|in:0,1,2',
                        'address_status'=>'required_if:verify_status,verify|in:0,1,3',
                        'citizenship_id'=>'required_if:verify_status,verify',
                        'email'=>'email',
                        'address'=>'required_if:verify_status,verify',
                        'mother_name'=>'required_if:verify_status,verify',
                        'phone'=>'string|regex:/^[0-9]+$/|max:15',
                        'mobile_phone'=>'string|regex:/^[0-9]+$/|max:15',
                        'couple_nik'=>'required_if:status,2',
                        'couple_name'=>'required_if:status,2',
                        'couple_birth_place_id'=>'required_if:status,2|exists:cities,id',
                        'couple_birth_date'=>'required_if:status,2',
                        'job_field_id'=>'required_if:verify_status,verify',
                        'job_type_id'=>'required_if:verify_status,verify',
                        'job_id'=>'required_if:verify_status,verify',
                        'company_name'=>'required_if:verify_status,verify',
                        'position'=>'required_if:verify_status,verify',
                        'work_duration'=>'required_if:verify_status,verify',
                        'office_address'=>'required_if:verify_status,verify',
                        'salary'=>'required_if:verify_status,verify',
                        'other_salary'=>'required_if:verify_status,verify',
                        'loan_installment'=>'required_if:verify_status,verify',
                        'dependent_amount'=>'required_if:verify_status,verify',
                        'couple_salary'=>'',
                        'couple_other_salary'=>'',
                        'couple_loan_installment'=>'',
                        'couple_identity'=>'required_if:status,2|image|mimes:jpg,jpeg,png',
                        'emergency_name'=>'required_if:verify_status,verify',
                        'emergency_mobile_phone'=>'required_if:verify_status,verify',
                        'emergency_relation'=>'required_if:verify_status,verify',
                        'identity'=>'required_if:verify_status,verify|image|mimes:jpg,jpeg,png',
                        'first_name'=>'',
                        'last_name'=>'',
                        'verify_status'=>'required|in:verify,verified',
                        'gender'=>'required_if:verify_status,verify'
                        
                        // 'verify_status' => 'required|in:verify,verified',
                        // 'cif_number' => '',
                        // 'first_name' => 'required_if:verify_status,verify',
                        // 'last_name' => '',
                        // 'gender' => 'required_if:verify_status,verify',
                        // // 'birth_place_id' => 'required_if:verify_status,verify',
                        // 'birth_date' => 'required_if:verify_status,verify',
                        // 'phone' => 'required_if:verify_status,verify',
                        // 'mobile_phone' => 'required_if:verify_status,verify|regex:(08)',
                        // 'address' => 'required_if:verify_status,verify',
                        // // 'citizenship_id' => 'required_if:verify_status,verify',
                        // // 'status' => 'required_if:verify_status,verify|in:0,1,2',
                        // 'address_status' => 'required_if:verify_status,verify',
                        // 'mother_name' => 'required_if:verify_status,verify'
                    ];
                } else {
                    return [
                        'nik' => 'required|numeric|digits:16|unique:customer_details,nik,' . $this->customer . ',user_id',
                        'email' => 'required|email|unique:users,email,' . $this->customer,
                        'first_name' => 'required',
                        'birth_place_id' => 'required',
                        'birth_date' => 'required|date',
                        'address' => 'required',
                        'gender' => 'required|in:L,P',
                        'city_id' => 'required|numeric|exists:cities,id',
                        'phone' => 'required',
                        'citizenship_id' => 'required',
                        'status' => 'required|in:0,1,2',
                        'couple_nik' => 'required_if:status,1|numeric|digits:16',
                        'couple_name' => 'required_if:status,1',
                        'couple_birth_place_id' => 'required_if:status,1',
                        'couple_birth_date' => 'required_if:status,1|date',
                        'couple_identity' => 'required_if:status,1|image',
                        'address_status' => 'required',
                        'mother_name' => 'required',
                        'mobile_phone' => 'required|regex:(08)',
                        'emergency_contact' => 'required',
                        'emergency_relation' => 'required',
                        'job_type_id' => 'required',
                        'job_id' => 'required',
                        'company_name' => 'required',
                        'job_field_id' => 'required',
                        'position' => 'required',
                        'work_duration' => 'required',
                        'office_address' => 'required',
                        'salary' => 'required|numeric',
                        'other_salary' => 'required|numeric',
                        'loan_installment' => 'required',
                        'dependent_amount' => 'required',
                        'legal_document' => 'required|file',
                        'salary_slip' => 'required|file',
                        'identity' => 'image|mimes:jpg,jpeg,png',
                        'image' => 'image|mimes:jpg,jpeg,png',
                        'npwp' => 'required|image|mimes:jpg,jpeg,png',
                        'bank_statement' => 'required|file',
                        'family_card' => 'required|file',
                        'marrital_certificate' => 'required_if:status,1|file',
                        'diforce_certificate' => 'required_if:status,2|file'
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
            return $this->except( [ 'couple_nik', 'couple_name', 'couple_birth_place_id', 'couple_birth_date', 'couple_identity' ] );
        }
        return $this->all();
    }
}
