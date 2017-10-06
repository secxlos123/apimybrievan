<?php

namespace App\Http\Requests\API\v1\ThirdParty;
use App\Http\Requests\BaseRequest as FormRequest;

class ThirdPartyRequest extends FormRequest
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

     /* Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return[
            'name' => 'required|string|regex:/^[a-zA-Z._ -]+$/|unique:third_parties,name|min:5|max:150',
            'address' => 'required|string',
            'city_id' => 'required|integer|exists:cities,id',
            'phone_number' => 'required|string|regex:/^[0-9]+$/|max:15',
            'email' => 'required|email|unique:third_parties,email|max:150',
            'is_actived' => 'string',
        ];
    }

}
