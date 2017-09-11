<?php

namespace App\Http\Requests\API\v1\Eks;

use App\Http\Requests\BaseRequest;

use Sentinel;

class AuthRequest extends BaseRequest
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
                if( $this->segment( 5 ) == 'register' ) {
                    return [
                        'email' => 'required|email|unique:users,email',
                        'password' => 'required|min:6',
                    ];
                } else if ( $this->segment( 5 ) == 'login' ) {
                    return [
                        'email' => 'required|email',
                        'password' => 'required',
                    ];
                } else  if( $this->segment( 3 ) == 'activate' ) {
                    return [
                        'user_id' => 'required|exists:users,id',
                        'code' => 'required|exists:activations,code,completed,false'
                    ];
                } else if ( $this->segment( 5 ) == 'register-complete' ) {
                    $login_session = Sentinel::getUser();
                    $additional = '';
                    if( $customer_detail = $login_session->customer_detail ) {
                        $additional = ',' . $customer_detail->id;
                    }
                    return [
                        'nik' => 'required|numeric|digits:16|unique:customer_details,nik' . $additional,
                        'first_name' => 'required',
                        'last_name' => '',
                        'birth_place' => 'required',
                        'birth_date' => 'required|date',
                        'address' => 'required',
                        'gender' => 'required|in:L,P',
                        'city' => 'required',
                        'phone' => 'required|numeric|digits:12',
                        'citizenship' => 'required',
                        'status' => 'required|in:0,1,2',
                        'address_status' => 'required',
                        'mother_name' => 'required',
                        'mobile_phone' => 'required|numeric|digits:12',
                        'emergency_contact' => 'required|numeric|digits:12',
                        'emergency_relation' => 'required',
                        'identity' => 'required|image|mimes:jpg,jpeg,png',
                        'npwp' => 'required|image|mimes:jpg,jpeg,png',
                        'image' => 'required|image|mimes:jpg,jpeg,png',
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
                } else if ( $this->segment( 5 ) == 'register-simple' ) {
                    $login_session = Sentinel::getUser();
                    $additional = '';
                    if( $customer_detail = $login_session->customer_detail ) {
                        $additional = ',' . $customer_detail->id;
                    }
                    return [
                        'nik' => 'required|numeric|digits:16|unique:customer_details,nik' . $additional,
                        'first_name' => 'required',
                        'last_name' => '',
                        'mobile_phone' => 'required|numeric|digits:12',
                        'status' => 'required|in:0,1,2',
                        'mother_name' => 'required',
                        'identity' => 'required|image',
                        'couple_nik' => 'required_if:status,1|numeric|digits:16',
                        'couple_name' => 'required_if:status,1',
                        'birth_place' => 'required_if:status,1',
                        'birth_date' => 'required_if:status,1|date',
                        'couple_identity' => 'required_if:status,1|image'
                    ];
                } else {
                    return [];
                }
                break;
            default:
                return [];
                break;
        };
    }
}
