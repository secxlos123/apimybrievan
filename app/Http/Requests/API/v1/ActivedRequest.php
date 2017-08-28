<?php

namespace App\Http\Requests\API\v1;

use App\Http\Requests\BaseRequest as FormRequest;

class ActivedRequest extends FormRequest
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
            'is_actived' => 'required|boolean'
        ];
    }
}
