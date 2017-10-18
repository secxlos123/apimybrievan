<?php

namespace App\Http\Requests\API\v1\DeveloperAgent;

use App\Http\Requests\API\v1\DeveloperAgent\BaseRequest as FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return parent::rules();
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $this->replace($this->except(['email', '_method']));
        return parent::getValidatorInstance();
    }
}
