<?php

namespace App\Http\Requests\API\v1\Dropdown;

use App\Http\Requests\API\v1\Dropdown\BaseRequest;

class PropTypesRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'property_id' => 'required|exists:properties,id'
        ];

        if ( $this->user() ) {
            if ( $this->user()->inRole('developer') ) {
                $dev_id = $this->user()->developer->id;
                $rules['property_id'] = "required|exists:properties,id|developer_owned:{$dev_id}";
            }
        }
        
        return $rules;
    }
}
