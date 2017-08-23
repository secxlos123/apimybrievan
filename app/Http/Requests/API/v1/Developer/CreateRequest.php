<?php

namespace App\Http\Requests\API\v1\Developer;

use App\Http\Requests\API\v1\Developer\BaseRequest as FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'company_name'  => 'required|alpha_spaces', 'email' => 'required|email|unique:users,email',
        ]);
    }
}
