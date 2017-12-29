<?php

namespace App\Http\Requests\API\v1\Collateral;

use App\Http\Requests\BaseRequest as FormRequest;

class CreateOtsDoc extends FormRequest
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
        return [
        'collateral_binding_doc'=>'mimes:jpeg,png,jpg,zip,pdf',
        'collateral_insurance_doc'=>'mimes:jpeg,png,jpg,zip,pdf',
        'life_insurance_doc'=>'mimes:jpeg,png,jpg,zip,pdf',
        'ownership_doc'=>'mimes:jpeg,png,jpg,zip,pdf',
        'building_permit_doc'=>'mimes:jpeg,png,jpg,zip,pdf',
        'sales_law_doc'=>'mimes:jpeg,png,jpg,zip,pdf',
        'property_tax_doc'=>'mimes:jpeg,png,jpg,zip,pdf',
        'sale_value_doc'=>'mimes:jpeg,png,jpg,zip,pdf',
        'progress_one_doc'=>'mimes:jpeg,png,jpg,zip,pdf',
        'progress_two_doc'=>'mimes:jpeg,png,jpg,zip,pdf',
        'progress_three_doc'=>'mimes:jpeg,png,jpg,zip,pdf',
        'progress_four_doc'=>'mimes:jpeg,png,jpg,zip,pdf',
        'progress_five_doc'=>'mimes:jpeg,png,jpg,zip,pdf'
        ];
    }
}
