<?php

namespace App\Http\Requests\API\v1\DeveloperAgent;

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
            'name'                  => 'required|alpha_spaces',
            'email'                 => 'required|email|unique:users,email',
            'birth_date'            => 'required|date',
            'join_date'             => 'required|date',
            'admin_developer_id'    => 'required'
        ];
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $role_id = \Sentinel::findRoleBySlug('developer-sales')->id;
        list($first_name, $last_name) = name_separator($this->input('name'));
        $this->merge( compact( 'role_id', 'first_name', 'last_name' ) );
        return parent::getValidatorInstance();
    }
}
