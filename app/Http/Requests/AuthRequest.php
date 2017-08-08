<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;

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
                return [
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|min:6',
                    'first_name' => 'required',
                    'last_name' => 'required'
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
