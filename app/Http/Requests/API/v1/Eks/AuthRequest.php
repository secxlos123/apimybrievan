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
                        'is_simple' => 'required|in:0,1',
                        'nik' => 'required|numeric|digits:16|unique:customer_details,nik' . $additional,
                        'first_name' => 'required',
                        'last_name' => '',
                        'birth_place' => 'required',
                        'birth_date' => 'required|date',
                        'address' => 'required',
                        'gender' => 'required|in:L,P',
                        'city' => 'required',
                        'phone' => 'required|numeric|digits_between:9,16',
                        'citizenship' => 'required',
                        'status' => 'required|in:1,2,3',
                        'address_status' => 'required',
                        'mother_name' => 'required',
                        'mobile_phone' => 'required|numeric|digits_between:9,16',
                        'emergency_contact' => 'required|numeric|digits_between:9,16',
                        'emergency_relation' => 'required',
                        'work_type' => 'required',
                        'work' => 'required',
                        'company_name' => 'required',
                        'work_field' => 'required',
                        'position' => 'required',
                        'work_duration' => 'required',
                        'office_address' => 'required',
                        'salary' => 'required',
                        'other_salary' => 'required',
                        'loan_installment' => 'required',
                        'dependent_amount' => 'required',
                        'identity' => 'required_if:is_simple,0|image|mimes:jpg,jpeg,png',
                        'npwp' => 'image|mimes:jpg,jpeg,png',
                        'image' => 'image|mimes:jpg,jpeg,png',
                        'couple_nik' => 'required_if:status,2|numeric|digits:16',
                        'couple_name' => 'required_if:status,2',
                        'couple_birth_place_id' => 'required_if:status,2',
                        'couple_birth_date' => 'required_if:status,2|date',
                        'couple_identity' => 'required_if:is_simple,0|required_if:status,2|image'
                    ];
                } else if ( $this->segment( 5 ) == 'register-simple' ) {
                    $login_session = Sentinel::getUser();
                    $additional = '';
                    if( $customer_detail = $login_session->customer_detail ) {
                        $additional = ',' . $customer_detail->id;
                    }
                    return [
                        'is_simple' => 'required|in:0,1',
                        'nik' => 'required|numeric|digits:16|unique:customer_details,nik' . $additional,
                        // 'email' => 'required|email',
                        'first_name' => 'required',
                        'last_name' => '',
                        'mobile_phone' => 'required|numeric|digits_between:9,16',
                        'status' => 'required|in:1,2,3',
                        'mother_name' => 'required',
                        'birth_place_id' => 'required',
                        'birth_date' => 'required|date',
                        'identity' => 'required_if:is_simple,0|image|mimes:jpg,jpeg,png',
                        'couple_nik' => 'required_if:status,2|numeric|digits:16',
                        'couple_name' => 'required_if:status,2',
                        'couple_birth_place_id' => 'required_if:status,2',
                        'couple_birth_date' => 'required_if:status,2|date',
                        'couple_identity' => 'required_if:is_simple,0|required_if:status,2|image'
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

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData()
    {
        if( $this->has( 'status' ) & $this->status != '2' ) {
            return $this->except( [ 'couple_nik', 'couple_name', 'couple_birth_place', 'couple_birth_date', 'couple_identity' ] );
        }
        return $this->all();
    }
}
