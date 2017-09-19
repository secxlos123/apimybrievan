<?php

namespace App\Http\Requests\API\v1\Dropdown;

use App\Http\Requests\API\v1\Dropdown\BaseRequest;

class PropertyRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'dev_id' => 'required|exists:users,id'
        ];
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        if ( $this->user() ) {
            if ( $this->user()->inRole('developer') ) {
                $dev_id = $this->user()->id;
                $this->merge(compact('dev_id'));
            }
        }
        
        return parent::getValidatorInstance();
    }
}
