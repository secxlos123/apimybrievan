<?php

namespace App\Http\Requests\API\v1\Profile;

use App\Http\Requests\BaseRequest as FormRequest;

class UpdateRequest extends FormRequest
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
        $id = $this->user()->id;

        return [
            'name' => 'required|alpha_spaces',
            'company_name' => 'required',
            'email' => "required|email|unique:users,email,{$id}",
            'phone' => 'required|digits:12|numeric',
            'mobile_phone' => 'required|digits:12|numeric',
            'city_id' => 'required|exists:cities,id',
            'address' => 'required',
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
        $this->replace( $this->except(['_method']) );
        return parent::getValidatorInstance();
    }
}
