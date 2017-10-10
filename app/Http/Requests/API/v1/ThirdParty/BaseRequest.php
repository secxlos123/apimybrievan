<?php

namespace App\Http\Requests\API\v1\ThirdParty;
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

     /* Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return[
            'address' => 'required|string',
            'city_id' => 'required|integer|exists:cities,id',
            'phone_number' => 'required|string|regex:/^[0-9]+$/|max:15',
        ];
    }

     /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $role_id = \Sentinel::findRoleBySlug('others')->id;
        list($first_name, $last_name) = name_separator($this->input('name'));
        $this->merge( compact( 'role_id', 'first_name', 'last_name' ) );
        return parent::getValidatorInstance();
    }

}
