<?php

namespace App\Http\Requests\API\v1\Developer;

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
            'city_id'       => 'required|exists:cities,id',
            'developer_name'=> 'required|alpha_spaces',
            'company_name'  => 'required|alpha_spaces',
            'address'       => 'required',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'required|digits:12|numeric',
            'mobile_phone'  => 'required|digits:12|numeric',
            'image'         => 'image|max:1024',
        ];
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $role_id = \Sentinel::findRoleBySlug('developer')->id;
        list($first_name, $last_name) = name_separator($this->input('developer_name'));
        $this->merge( compact( 'role_id', 'first_name', 'last_name' ) );
        return parent::getValidatorInstance();
    }
}
