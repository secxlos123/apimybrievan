<?php

namespace App\Http\Requests\API\v1\Crm\Marketing;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'pn' => 'required',
            'product_type' => 'required',
            'activity_type' => 'required',
            'target' => 'required',
            'account' => 'required',
            'status' => 'required',
            'target_closing_date' => 'required'
        ];
    }
}
