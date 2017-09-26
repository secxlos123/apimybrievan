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
                        'ao_id' => 'required',
                    ];
                } else if( $this->segment( 6 ) == 'approve' ) {
                    return [
                        'pros' => 'required',
                        'cons' => 'required'
                    ];
                } else {
                    return [
                        'product_type' => 'required|in:kpr',
                        'status_property' => 'required_if:product_type,kpr,required|in:new,second',
                        'developer' => 'required_if:product_type,kpr,required_if:status_property,new',
                        'property' => 'required_if:product_type,kpr,required_if:status_property,new',
                        'price' => 'required_if:product_type,kpr,required|numeric',
                        'building_area' => 'required_if:product_type,kpr,required|numeric',
                        'home_location' => 'required_if:product_type,kpr,required',
                        'year' => 'required_if:product_type,kpr,required|numeric',
                        'active_kpr' => 'required_if:product_type,kpr,required|numeric',
                        'dp' => 'required_if:product_type,kpr,required|numeric',
                        'request_amount' => 'required_if:product_type,kpr,required|numeric',
                        'nik' => 'required|exists:customer_details,nik',
                        'branch_id' => 'required',
                        'appointment_date' => 'required|date',
                        'address' => 'required',
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
