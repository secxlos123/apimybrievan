<?php

namespace App\Http\Requests\API\v1;

use App\Http\Requests\BaseRequest;

class ScoringRequest extends BaseRequest
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
                return [
                    'id' => 'required',
					'pefindo_score' => 'required|numeric',
                     ];
                break;
            
            case 'put':
                if( $this->segment( 6 ) == 'verify' ) {
                    return [
                    'id' => 'required',
					'pefindo_score' => 'required|numeric',
                    ];
                } else {
                    return [
                    'id' => 'required',
					'pefindo_score' => 'required|numeric',
                    ];
                }
                break;
            
            default:
                return [
                    //
                ];
                break;
        }
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData()
    {
     
        return $this->all();
    }
}
