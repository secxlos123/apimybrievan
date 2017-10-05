<?php

namespace App\Http\Requests\API\v1\ThirdParty;

use App\Http\Requests\BaseRequest as FormRequest;

class UpdateThirdPartyRequest extends FormRequest
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
            'nama_perusahaan' => 'required|string|regex:/^[a-zA-Z._ -]+$/|min:5|max:150',
            'alamat_perusahaan' => 'required|string',
            'city_id' => 'required|integer|exists:cities,id',
            'no_telp' => 'required|string|regex:/^[0-9]+$/|max:15',
            'email' => 'required|email|max:150',
            'status' => 'string',
        ];
    }

}
