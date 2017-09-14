<?php

namespace App\Http\Requests\API\v1\Property;

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
        if ($this->method() == 'PUT') {
            return $this->user()->developer->id == $this->property->developer_id;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $request = $this;

        $ruleName = Rule::unique('properties', 'name')->using(function ($query) use ($request) {
            $query->where('city_id', $request->input('city_id'));
            if ($request->method() == 'PUT') $query->where('id', '<>', $request->property->id);
        });

        $rules = [
            'name'       => ['required', $ruleName],
            'city_id'    => 'required|exists:cities,id',
            'address'    => 'required',
            'category'   => 'required|in:apartment,ruko,rumah,vila,kantor,komersial',
            'latitude'   => 'required',
            'longitude'  => 'required',
            'facilities' => 'required',
            'pic_name'   => 'required|alpha_spaces',
            'pic_phone'  => 'required|digits:12|numeric',
            'photo'      => 'required|image|max:1024',
        ];

        if ($request->method() == 'PUT') {
            $rules['photo'] = 'image|max:1024';
        }

        return $rules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @todo if cannot use, can delete this function
     * @return array
     */
    public function childRules()
    {
        $property_types = PropertyType::$rules;
        $property_items = PropertyItem::$rules;

        foreach ($property_types as $key => $value) {
            $property_types["property_types.*.{$key}"] = $value;
            unset($property_types[$key]);
        }

        foreach ($property_items as $key => $value) {
            $property_items["property_types.*.property_items.*.{$key}"] = $value;
            unset($property_items[$key]);
        }

        return array_merge($property_items, $property_types);
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        if ($this->method() != 'PUT') {
            $developer_id = $this->user()->developer->id;
            $this->merge(compact('developer_id'));
        }
        return parent::getValidatorInstance();
    }
}
