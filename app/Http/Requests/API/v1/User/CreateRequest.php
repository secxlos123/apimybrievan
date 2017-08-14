<?php

namespace App\Http\Requests\API\v1\User;

use App\Http\Requests\BaseRequest as FormRequest;

class CreateRequest extends FormRequest
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
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|numeric',
            'mobile_phone' => 'required|numeric',
            'gender' => 'required|in:L,P',
            'office_id' => 'required|exists:offices,id',
            'role_id' => 'required|exists:roles,id',
            'nip' => 'required|unique:user_details,nip',
            'position' => 'required',
        ];
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $this->merge(['password' => bcrypt(str_random(8))]);
        return parent::getValidatorInstance();
    }
}
