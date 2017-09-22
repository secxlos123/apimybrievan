<?php

namespace App\Http\Requests\API\v1\Developer;

use App\Http\Requests\API\v1\Developer\BaseRequest as FormRequest;

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
            'company_name'  => 'required', 'email' => 'required|email|unique:users,email',
        ]);
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $this->merge([ 'created_by' => $this->header('pn') ]);
        return parent::getValidatorInstance();
    }
}
