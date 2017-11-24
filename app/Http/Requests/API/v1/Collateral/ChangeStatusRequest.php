<?php

namespace App\Http\Requests\API\v1\Collateral;

use App\Http\Requests\BaseRequest as FormRequest;

class ChangeStatusRequest extends FormRequest
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
        if ($this->segment(5) === 'reject') {
          return [
              'remark' => 'required'
          ];
        }
        return [];
    }
}
