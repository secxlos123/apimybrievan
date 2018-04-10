<?php

namespace App\Http\Requests\API\v1\DeveloperAgent;

use App\Http\Requests\API\v1\DeveloperAgent\BaseRequest as FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            'mobile_phone' => 'required|string|regex:/^[0-9]+$/|max:15',
            'email' => 'required|email|unique:users,email'

        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return array_merge(parent::rules(), [
            'mobile_phone.required' => 'Kolom No Handphone harus diisi!',
            'email.unique' => 'Email sudah pernah digunakan!'

        ]);
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $this->merge([ 'admin_developer_id' => $this->user()->id ]);
        return parent::getValidatorInstance();
    }
}
