<?php

namespace App\Http\Requests\API\v1\Dropdown;

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
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $this->merge(['dropdown' => true, 'limit' => $this->input('limit') ?: 10]);
        return parent::getValidatorInstance();
    }
}
