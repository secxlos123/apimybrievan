<?php

namespace App\Http\Requests\API\v1\PropertyType;

use App\Http\Requests\BaseRequest as FormRequest;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
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
        $property_types = PropertyType::$rules;
        $request = $this;

        $property_types['property_id'] = [
            'required',
            Rule::exists('properties', 'id')->using(function ($property) use ($request) {
                $user = $request->user()->developer->id;
                $property->where('developer_id', $user);
            })
        ];
        
        if ($this->method() == 'PUT') unset($property_types['photos']);

        return $property_types;
    }
}
