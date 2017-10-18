<?php

namespace App\Http\Requests\API\v1\DeveloperAgent;

use App\Http\Requests\API\v1\DeveloperAgent\BaseRequest as FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return parent::rules();
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $this->merge([ 'admin_developer_id' => $this->user()->id ]);
        return parent::getValidatorInstance();
    }
}
