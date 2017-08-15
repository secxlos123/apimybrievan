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
                    'nik' => 'required|unique:customer_details,nik',
                    'email' => 'required|email|unique:users,email',
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'birth_place' => 'required',
                    'birth_date' => 'required|date',
                    'address' => 'required',
                    'gender' => 'required|in:L,P',
                    'city' => 'required',
                    'phone' => 'required|regex:(08)',
                    'citizenship' => 'required',
                    'status' => 'required|in:0,1,2',
                    'address_status' => 'required',
                    'mother_name' => 'required',
                    'mobile_phone' => 'required|regex:(08)',
                    'emergency_contact' => 'required|regex:(08)',
                    'emergency_relation' => 'required',
                    'identity' => 'required|numeric',
                    'npwp' => 'required',
                    'work_type' => 'required',
                    'work' => 'required',
                    'company_name' => 'required',
                    'work_field' => 'required',
                    'position' => 'required',
                    'work_duration' => 'required',
                    'office_address' => 'required',
                    'salary' => 'required|integer',
                    'other_salary' => 'required|integer',
                    'loan_installment' => 'required',
                    'dependent_amount' => 'required'
                ];
                break;
            
            case 'put':
                return [
                    'nik' => 'required|unique:customer_details,nik,' . $this->customer . ',user_id',
                    'email' => 'required|email|unique:users,email,' . $this->customer,
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'birth_place' => 'required',
                    'birth_date' => 'required|date',
                    'address' => 'required',
                    'gender' => 'required|in:L,P',
                    'city' => 'required',
                    'phone' => 'required|regex:(08)',
                    'citizenship' => 'required',
                    'status' => 'required|in:0,1,2',
                    'address_status' => 'required',
                    'mother_name' => 'required',
                    'mobile_phone' => 'required|regex:(08)',
                    'emergency_contact' => 'required|regex:(08)',
                    'emergency_relation' => 'required',
                    'identity' => 'required|numeric',
                    'npwp' => 'required',
                    'work_type' => 'required',
                    'work' => 'required',
                    'company_name' => 'required',
                    'work_field' => 'required',
                    'position' => 'required',
                    'work_duration' => 'required',
                    'office_address' => 'required',
                    'salary' => 'required|integer',
                    'other_salary' => 'required|integer',
                    'loan_installment' => 'required',
                    'dependent_amount' => 'required'
                ];
                break;
            
            default:
                return [
                    //
                ];
                break;
        }
    }
}
