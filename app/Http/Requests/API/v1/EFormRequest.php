<?php

namespace App\Http\Requests\API\V1;

use App\Http\Requests\BaseRequest;

class EFormRequest extends BaseRequest
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
        switch ( strtolower( $this->method() ) ) {
            case 'post':
                if( $this->segment(6) == 'disposition' ) {
                    return [
                        'ao_id' => 'required|exists:users,id',
                    ];
                } else {
                    return [
                        'nik' => 'required|exists:customer_details,nik',
                        'office_id' => 'required|exists:offices,id',
                        'product' => 'required|json',
                        'appointment_date' => 'required|date',
                        'longitude' => 'required',
                        'latitude' => 'required'
                    ];
                }
                break;
            
            case 'put':
                return [
                    'id' => 'required|exists:eforms,id',
                    'prescreening_status' => 'required|integer'
                ];
                break;
            
            default:
                return [
                    //
                ];
                break;
        }
    }
}
