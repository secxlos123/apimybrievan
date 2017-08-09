<?php

namespace App\Http\Requests\API\v1\Role;

use App\Http\Requests\API\v1\Role\BaseRequest as FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = array_merge(parent::rules(), ['slug' => 'required|unique:roles,slug']);
        return array_merge($rules, $this->permissions());
    }
}
