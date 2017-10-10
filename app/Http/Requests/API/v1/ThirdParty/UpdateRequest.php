<?php

namespace App\Http\Requests\API\v1\ThirdParty;

use App\Http\Requests\API\v1\ThirdParty\BaseRequest as FormRequest;

class UpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
       return array_merge(parent::rules(), [
            'name' => 'required|string|regex:/^[a-zA-Z._ -]+$/|unique:third_parties,name|min:5|max:150',

        ]);
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $this->replace($this->except(['email','_method']));
        return parent::getValidatorInstance();
    }

}
