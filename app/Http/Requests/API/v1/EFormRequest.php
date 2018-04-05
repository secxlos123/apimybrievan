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
        \Log::info($this->all());
        if ($this->input('developer')) {
            if ( $this->input('developer') == ENV('DEVELOPER_KEY', 1) ) {
                $property = '';
                $kpr_type_property = 'required';
            } else {
                $kpr_type_property = '';
                $property = 'required_unless:developer,1';
            }
        } else {
            $kpr_type_property = '';
            $property = '';
        }
        switch ( strtolower( $this->method() ) ) {
            case 'post':
                if( $this->segment(6) == 'disposition' ) {
                    return [
                        'ao_id' => 'required',
                    ];
                } else if( $this->segment( 6 ) == 'approve' ) {
                    return [
                        'recommendation'=> 'required'
                    ];
                } else {
                    return [
                        /* changing */
                        'product_type' => 'required|in:kpr,briguna,kartu_kredit',
                        /* ---------------- */
                        /* BRIGUNA */
                        'status_property' => 'required_if:product_type,kpr,required',
                        'idMitrakerja' => 'required_if:product_type,briguna,required',
                        'tujuan_penggunaan' => 'required_if:product_type,briguna,required',
                        /*-----------------------*/
                        'status_property' => 'required_if:product_type,kpr,required',
                        'developer' => 'required_if:status_property,1',
                        'kpr_type_property' => $kpr_type_property,
                        'property' => $property,
                        'price' => 'required_if:product_type,kpr,required|numeric',
                        'building_area' => 'required_if:product_type,kpr,required|numeric',
                        'home_location' => 'required_if:product_type,kpr,required',
                        'year' => 'required_if:product_type,kpr,required|numeric',
                        'active_kpr' => 'required_if:product_type,kpr,required|numeric',
                        'dp' => 'required_if:product_type,kpr,required',
                        'request_amount' => 'required_if:product_type,kpr,required',
                        'nik' => 'required',
                        'branch_id' => 'required',
                        'appointment_date' => 'required|date',
                        'address' => 'required_if:product_type,kpr,briguna',
                        'longitude' => 'required_if:product_type,kpr,brigunas',
                        'latitude' => 'required_if:product_type,kpr,briguna',
                        
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
