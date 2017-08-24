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
        $detail = $this->user->detail ? ",{$this->user->detail->id}" : '';

        return array_merge(parent::rules(), [
            'nip' => "required|min:16|max:16|unique:user_details,nip{$detail}",
            'email' => "required|email|unique:users,email,{$this->user->id}",
        ]);
    }
}
