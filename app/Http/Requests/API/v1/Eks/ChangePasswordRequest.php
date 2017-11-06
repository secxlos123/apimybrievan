<?php

namespace App\Http\Requests\API\v1\Eks;

use App\Http\Requests\BaseRequest;

class ChangePasswordRequest extends BaseRequest
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
        return
        [
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed'

        ];   
    }

}
