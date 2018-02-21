<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\v1\Int\Crm\CustomerController;
use App\Http\Controllers\API\v1\EFormController;

class KartuKreditController extends Controller{
	
	public function example(){

		return response()->json([
                'name' => 'Abigail',
                'state' => 'CA'
            ]);
		
		
	}

	public function cekEform(Request $request){
		$ef = new EFormController();
		$ef.store($request);
	}

	public function getNiks(Request $request){
		$nik = $request['nik'];
		if ($nik == '123'){
			return response()->json([
				'code'=>'200',
				'nik'=>$nik
			]);
		}

		return "salah";
	}

	public function requestNikFromCRM(Request $request){
		$crm = new CustomerController();
		$response = $crm->customer_nik($request);

		return response;
	}


	public function getNikFromMyBriDb($nik){
			//cek nik di database nasabah
	}

	public function getNikFromCrmDb($nik){
	
		//tangkap nik dan cek di database customer crm
	}
}

 