<?php

namespace App\Http\Requests\API\v1\Appointment;

use App\Http\Requests\BaseRequest as FormRequest;

class CreateRequest extends FormRequest
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
          'title' => 'required',
          'appointment_date' => 'required|date',
          'user_id' => 'required|regex:/^[0-9]+$/',
          'ao_id' => 'required|regex:/^[0-9]+$/',
          'eform_id' => 'required|regex:/^[0-9]+$/',
          'ref_number' => 'required',
          'address' => 'required',
          'latitude' => 'required',
          'longitude' => 'required',
          'guest_name' => 'required',
          'desc' => 'required'
        ];
    }
}
