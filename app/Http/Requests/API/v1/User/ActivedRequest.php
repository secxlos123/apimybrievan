<?php

namespace App\Http\Requests\API\v1\User;

use App\Http\Requests\API\v1\User\BaseRequest as FormRequest;

class ActivedRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'is_actived' => 'required|boolean'
        ];
    }
}
