<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Dropbox;
use Illuminate\Http\Request;

class DropboxController extends Controller
{
    public function index(Request $request) {
    	print_r($request->all());exit();
    	$Dropbox = new Dropbox();
    	$respons = $request->all();
    	$method  = $respons['requestMethod'];
    	$data	 = $respons['requestData'];

    	switch ($method) {
    		case 'insertSkpp':
    			$postData = [
		            'requestMethod' => $method,
		            'requestData'   => json_encode([
		                'branch'  	=> $data,
		                'appname' 	=> 'MBR',
		                'jenis'   	=> 'BG',
		                'expdate' 	=> date('Y-m-d'),
		                'content' 	=> $data,
		                'status'  	=> '1'
		            ])
		        ];

		        $data_dropbox = $Dropbox->insertDropbox($postData);
    			break;
    	
    		default:
    			# code...
    			break;
    	}
    }
}
