<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RestwsHc;

class CustomerController extends Controller
{
	/**
	 * [kemendagri description]
	 * 
	 * @param  Request $request [description]
	 * @return [type]           [description]
	 */
    public function kemendagri(Request $request)
    {
    	$kemendagri = RestwsHc::setBody([
    		'request' => json_encode([
    			'requestMethod' => 'get_kemendagri_profile_nik',
    			'requestData' => [
    				'nik' 	  => '3277022606930014',
    				'id_user' => $request->header('pn')
    			],
    		])
    	])
    	->setHeaders([
    		'Authorization' => $request->header('Authorization')
    	])->post('form_params');

    	return response()->success(['contents' => $kemendagri]);
    }

    /**
     * [customer description]
     * 
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function customer(Request $request)
    {
        $customer = RestwsHc::setBody([
            'request' => json_encode([
                'requestMethod' => 'get_customer_profile_nik',
                'requestData'   => ['nik' => '3274032403920004'],
            ])
        ])->post('form_params');

        return response()->success(['contents' => $customer]);
    }
}
