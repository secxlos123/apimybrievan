<?php

namespace App\Http\Requests\API\v1\User;

use App\Http\Requests\API\v1\User\BaseRequest as FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'nip' => 'required|unique:user_details,nip',
            'email' => 'required|email|unique:users,email',
        ]);
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
