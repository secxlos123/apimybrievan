<?php

namespace App\Http\Requests\API\v1\Developer;

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
            'developer_name'=> 'required|alpha_spaces',
            'city_id'       => 'required|exists:cities,id',
            'address'       => 'required',
            // 'phone'         => 'required',
            'mobile_phone'  => 'required|string|regex:/^[0-9]+$/|min:9|max:12',
            'image'         => 'image|max:1024'
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
