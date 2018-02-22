<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\v1\Int\Crm\CustomerController;
use App\Http\Controllers\API\v1\EFormController;
use App\Models\CustomerDetail;
use App\Models\KartuKredit;
use GuzzleHttp\Client;

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

	public function statusPernikahan(Request $req){
		//arahin ke los
		$TOKEN_LOS = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJsb3NhcHAiLCJhY2Nlc3MiOlsidGVzIl0sImp0aSI6IjhjNDNlMDNkLTk5YzctNDJhMC1hZDExLTgxODUzNDExMWNjNCIsImlhdCI6MTUxODY2NDUzOCwiZXhwIjoxNjA0OTc4MTM4fQ.ocz_X3duzyRkjriNg0nXtpXDj9vfCX8qUiUwLl1c_Yo';
		$client = new Client();
		$res = $client->request('POST','10.107.11.111:9975/api/listStatusPernikahan', ['headers' =>  ['access_token'=>$TOKEN_LOS]]);
		$responseCode = $res->getStatusCode();
		if ($responseCode == 200){
			return $res->getBody()->getContents();
		}else{
			return response()->error([
                        'message' => 'Tetot'
                    ], 422 );
		}
	}

	public function checkNIK(Request $req){
		$nik = CustomerDetail::where('nik','=',$req->nik)->first();
		if ($nik == null){
			return "tidak ditemukan";
		}else{
			
			return "nik ada";
		}

	}

}

 