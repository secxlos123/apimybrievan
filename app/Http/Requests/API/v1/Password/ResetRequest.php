<?php

namespace App\Http\Requests\API\v1\Password;

use App\Http\Requests\BaseRequest as FormRequest;

class ResetRequest extends FormRequest
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
            'email' => "required|email|exists:users,email|email_by_type:{$this->type}"
        ];
    }
}
