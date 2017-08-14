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
                    'email' => 'required|email|unique:users,email',
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'birth_place' => 'required',
                    'birth_date' => 'required|date',
                    'address' => 'required',
                    'gender' => 'required',
                    'city' => 'required',
                    'phone' => 'required'
                ];
                break;
            
            case 'put':
                return [
                    'email' => 'required|email|unique:users,email,' . $this->customer,
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'birth_place' => 'required',
                    'birth_date' => 'required|date',
                    'address' => 'required',
                    'gender' => 'required',
                    'city' => 'required',
                    'phone' => 'required'
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
