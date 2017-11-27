<?php

namespace App\Http\Requests\API\v1\ApprovalDataChange;

use App\Http\Requests\BaseRequest as FormRequest;

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
            // 'city_id' => 'required|regex:/^[\d.]+$/|exists:cities,id',
            // 'company_name' => 'required',
            // 'summary' => 'required',
            // 'logo' => 'required|file',
            // 'phone' => 'required|regex:/^[\d.]+$/',
            // 'mobile_phone' => 'required|regex:/^[\d.]+$/'
        ];
    }
}
