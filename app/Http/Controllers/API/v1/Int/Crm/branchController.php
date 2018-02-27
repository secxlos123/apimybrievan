<?php

namespace App\Http\Controllers\API\v1\Int\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use RestwsHc;

class branchController extends Controller
{
	public function list_kanwil(Request $request)
	{
		$list_kanwil = RestwsHc::setBody([
            		'request' => json_encode([
                	'requestMethod' => 'get_list_kanwil',
                	'requestData' => [
                   		'app_id' => 'mybriapi'
                		],
            		])
        	])
		->post( 'form_params' );

		return $list_kanwil;
	}	
}
