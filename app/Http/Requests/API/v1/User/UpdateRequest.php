<?php

namespace App\Http\Requests\API\v1\User;

use App\Http\Requests\API\v1\User\BaseRequest as FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->user->load('detail');
        
        return array_merge(parent::rules(), [
            'nip' => "required|unique:user_details,nip,{$this->user->detail->id}",
            'email' => "required|email|unique:users,email,{$this->user->id}",
        ]);
    }
}
