<?php

namespace App\Http\Requests\API\v1\User;

use App\Http\Requests\BaseRequest as FormRequest;

class BaseRequest extends FormRequest
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
            'data' => 'required|json',           
            'first_name' => 'required',
            // 'phone' => 'required|digits:12|numeric',
            'mobile_phone' => 'required|string|regex:/^[0-9]+$/|min:9|max:12',
            'gender' => 'required|in:L,P',
            'office_id' => 'required|exists:offices,id',
            'role_id' => 'required|exists:roles,id',
            'position' => 'required',
            'image' => 'image|max:1024',
        ];
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $this->merge( (array) json_decode($this->input('data')) );
        return parent::getValidatorInstance();
    }
}
