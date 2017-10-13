<?php

namespace App\Http\Requests\API\v1\Appointment;

use App\Http\Requests\BaseRequest as FormRequest;

class StatusRequest extends FormRequest
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
            'status' => 'required|in:approved,waiting,rejected'
        ];
    }
}
