<?php

namespace App\Http\Requests\API\v1\Dropdown;

use App\Http\Requests\API\v1\Dropdown\BaseRequest;

class PropItemRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'property_type_id' => 'required|exists:property_types,id'
        ];
    }
}
