<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;

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
                if( $this->segment( 3 ) == 'activate' ) {
                    return [
                        'user_id' => 'required|exists:users,id',
                        'code' => 'required|exists:activations,code,completed,false'
                    ];
                }
                return [];
                break;
            
            default:
                return [
                    //
                ];
                break;
        }
    }
}
