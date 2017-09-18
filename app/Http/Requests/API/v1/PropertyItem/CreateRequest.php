<?php

namespace App\Http\Requests\API\v1\PropertyItem;

use App\Http\Requests\BaseRequest as FormRequest;
use App\Models\PropertyType;
use App\Models\PropertyItem;
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
        $property_item = PropertyItem::$rules;
        $developerId = $this->user()->developer->id;
        $property_item['property_type_id'] = "required|exists:property_types,id|developer_owned:{$developerId}";
        if ($this->method() == 'PUT') unset($property_item['photos']);
        return $property_item;
    }
}
