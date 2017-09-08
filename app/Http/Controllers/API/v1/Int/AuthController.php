<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RestwsHc;

class AuthController extends Controller
{
    /**
     * The user has been authenticated.
     *
     * @param 	\Illuminate\Http\Request $request
     * @return 	\Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
    	$request->merge(['endpoint' => 'login']);
    	$results = RestwsHc::setBody($request->input())->post('form_params');
    	
    	return $this->results($results);
    }

    /**
     * The user has been authenticated.
     *
     * @param 	\Illuminate\Http\Request $request
     * @return 	\Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
    	$request->merge(['endpoint' => 'logout']);
    	$results = RestwsHc::setBody($request->input())
    				->setHeaders(['Authorization' => $request->header('Authorization')])
    				->post('form_params');

    	return $this->results($results);
    }

    /**
     * The user has been authenticated.
     *
     * @param 	array $results
     * @return 	\Illuminate\Http\Response
     */
    public function results($results)
    {
    	if ("00" === $results['responseCode']) {
	    	return response()->success([
	    		'contents' 		=> $results['responseData'],
	    		'descriptions' 	=> $results['responseDesc']
	    	]);
    	}

    	return response()->error([
    		'descriptions' => $results['responseDesc']
    	], 401);
    }
}
