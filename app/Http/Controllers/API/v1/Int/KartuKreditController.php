<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\EFormRequest;
use App\Models\KartuKredit;
use App\Models\CustomerDetail;
use GuzzleHttp\Client;
use App\Models\UserServices;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\User;
use App\Models\EForm;
use Asmx;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Http\Requests\API\v1\KreditRequest;

use App\Models\KreditEmailGenerator;

class KartuKreditController extends Controller{

	public $hostLos = '10.107.11.111:9975';
	public $tokenLos = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJsb3NhcHAiLCJhY2Nlc3MiOlsidGVzIl0sImp0aSI6IjhjNDNlMDNkLTk5YzctNDJhMC1hZDExLTgxODUzNDExMWNjNCIsImlhdCI6MTUxODY2NDUzOCwiZXhwIjoxNjA0OTc4MTM4fQ.ocz_X3duzyRkjriNg0nXtpXDj9vfCX8qUiUwLl1c_Yo';
	
	public $hostPefindo = '10.35.65.167:6969';

	public function contohemail(){
      // QrCode::format('png')->size(200)->generate('Make me into a QrCode!', public_path().'/tempQrcode/qrcode.png');

      // return 'a';
		// $data = EForm::find(1)->kartukredit()->get();
		$data = EForm::where('product_type','kartu_kredit')->with('kartukredit')->get();
		return response()->json($data);
    }


	function checkUser($nik){
        $check = CustomerDetail::where('nik', $nik)->get();
        if(count($check) == 0){
            return response()->json([
            	//user gak ketemu. crate baru dulu
            		'responseCode'=>'01',
                    'message' => 'Data dengan nik tersebut tidak ditemukan'
                    ]);
        }
        return true;
	}

	function checkDedup($nik){

		 $header = ['access_token'=> $this->tokenLos];
			 $client = new Client();
			 try{
                $res = $client->request('POST',$this->hostLos, ['headers' =>  $header,
                        'form_params' => ['nik' => $nik]
                    ]);
            }catch (RequestException $e){
                return response()->error([
                    'responseCode'=>'99',
                    'responseMessage'=> $e->getMessage()
                ],400);
            }


            return true;
	}


	public function getAllInformation(){
		$TOKEN_LOS = $this->tokenLos;
		$client = new Client();
		$host = $this->hostLos;

		try{
			$statusPernikahan = $this->getListStatusPernikahan($TOKEN_LOS,$client,$host);
			$statusTempatTinggal = $this->getListTempatTinggal($TOKEN_LOS,$client,$host);
			$kategoriPekerjaan = $this->getListKategoriPekerjaan($TOKEN_LOS,$client,$host);
			$statusPekerjaan = $this->getListStatusPekerjaan($TOKEN_LOS,$client,$host);
			$jumlahKaryawan = $this->getListJumlahKaryawan($TOKEN_LOS,$client,$host);
			$hubunganKeluarga = $this->getListHubunganKeluarga($TOKEN_LOS,$client,$host);
			$listSubBidangUsaha = $this->getListSubBidangUsaha($TOKEN_LOS,$client,$host);

		}catch (RequestException $e){
			return response()->json([
				'responseCode' => '01',
				'responseMessage' => 'Terjadi Kesalahan. Silahkan Tunggu Beberapa Saat Dan Ulangi',
			]);
		}
		
		return response()->json([
			'responseCode'=>0,
			'responseMessage'=>'success',
			'list_pernikahan'=> $statusPernikahan,
			'list_status_tempat_tinggal' => $statusTempatTinggal,
			'list_kategori_pekerjaan' => $kategoriPekerjaan,
			'list_status_pekerjaan' => $statusPekerjaan,
			'list_jumlah_karyawan' => $jumlahKaryawan,
			'list_hubungan_keluarga' => $hubunganKeluarga,
			'list_sub_bidang_usaha' =>$listSubBidangUsaha
		]);
	}

	function getListStatusPernikahan($token,$client,$host){
		
		$res = $client->request('POST',$host.'/api/listStatusPernikahan', ['headers' =>  ['access_token'=>$token]]);

		$responseCode = $res->getStatusCode();
		if ($responseCode == 200){
			$body = $res->getBody();
			$obj = json_decode($body);
			$data = $obj->responseData;

			return $data;
		}else{
			//error
			return 'error ambil list pernikahan';
		}
	}

	function getListTempatTinggal($token,$client,$host){

		$res = $client->request('POST',$host.'/api/listTempatTinggal', ['headers' =>  ['access_token'=>$token]]);

		$body = $res->getBody();
		$obj = json_decode($body);
		$data = $obj->responseData;

		return $data;
	}

	function getListKategoriPekerjaan($token,$client,$host){

		$res = $client->request('POST',$host.'/api/listKategoriPekerjaan', ['headers' =>  ['access_token'=>$token]]);
		
		$body = $res->getBody();
		$obj = json_decode($body);
		$data = $obj->responseData;

		return $data;
	}

	function getListStatusPekerjaan($token,$client,$host){

		$res = $client->request('POST',$host.'/api/listStatusPekerjaan', ['headers' =>  ['access_token'=>$token]]);
		
		$body = $res->getBody();
		$obj = json_decode($body);
		$data = $obj->responseData;

		return $data;
	}

	function getListJumlahKaryawan($token,$client,$host){

		$res = $client->request('POST',$host.'/api/listJumlahKaryawan', ['headers' =>  ['access_token'=>$token]]);
		
		$body = $res->getBody();
		$obj = json_decode($body);
		$data = $obj->responseData;

		return $data;
	}

	function getListHubunganKeluarga($token,$client,$host){

		$res = $client->request('POST',$host.'/api/listHubunganKeluarga', ['headers' =>  ['access_token'=>$token]]);
		
		$body = $res->getBody();
		$obj = json_decode($body);
		$data = $obj->responseData;

		return $data;
	}

	function getListSubBidangUsaha($token,$client,$host){

		$res = $client->request('POST',$host.'/api/listsubbidangusaha', ['headers' =>  ['access_token'=>$token]]);
		
		$body = $res->getBody();
		$obj = json_decode($body);
		$data = $obj->responseData;

		return $data;
	}

	public function checkNIK(Request $req){
		$client = new Client();
		$host = 'apimybri.bri.co.id/api/v1';

		//body
		$nik = $req['nik'];
		//header
		$pn =  $req->header('pn');
		// $branch = $req->header('branch');
		$auth = $req->header('Authorization');
		
		try{
			$res = $client
			->request('POST',
				$host.'/int/crm/account/customer_nik',
				['form_params'=>['nik' => $nik,],
				'headers'=>['pn'=> $pn,'Authorization'=>$auth]
				]
			);
		}catch(RequestException $e){


      	 	return response()->json([
      	 		'responseCode'=>'99',
				'responseMessage'=>$e->getMessage()
      	 	]);
		}

		$body = $res->getBody();
		$obj = json_decode($body);
		$contents = $obj->contents;

		return response()->json([
			'responseCode' => 00,
			'responseMessage' => 'sukses',
			'contents'=>$contents
		]);

		return $body;
		// return response()->json([
		// 	'nik'=>$nik,
		// 	'pn' =>$pn,
		// 	'Authorization'=>$auth

		// ]);
	}

	public function sendUserDataToLos(Request $req){

		$TOKEN_LOS = $this->tokenLos;

		$host = '10.107.11.111:9975';

		$validatedData = $this->validate($req,[
            'PersonalName' => 'required',
            'PersonalNIK' => 'required',
            'PersonalTempatLahir' => 'required',
            'PersonalTanggalLahir' => 'required',
        ]);

		$kk = new KartuKredit();
		$informasiLos = $kk->convertToAddDataLosFormat($req,'add');

		$client = new Client();

		try{
			$res = $client
			->request('POST',
				$host.'/api/addData',
				['headers'=>['access_token'=> $TOKEN_LOS],
				'form_params'=> $informasiLos,
				]
			);
		}catch (RequestException $e){

			return  $e->getMessage();
		}

		$body = $res->getBody();
		$obj = json_decode($body);
		$data = $obj->responseData;
		$appregno = $data->apRegno;

		$this->updateAppRegnoKreditDetails($appregno,$req->eform_id);

		return response()->json($obj);
    }

    function updateAppRegnoKreditDetails($appregno,$eform_id){
    	$addAppregno = KartuKredit::where('eform_id',$eform_id)
    	->update(['appregno'=>$appregno]);

    }

    public function updateDataLos(KreditRequest $req){
    	//saat verifikasi
    	$header = ['access_token'=> $this->tokenLos];
    	$host = '10.107.11.111:9975/api/updateData';
    	$client = new Client();
    	
    	$request = $req->all();
    	$eform_id = $request['eform_id'];
    	$request['appNumber'] = $this->getApregnoFromKKDetails($eform_id);

    	
    	$kk = new KartuKredit();
    	$informasiLos = $kk->convertToAddDataLosFormat($request,'update');

    	try{
			$res = $client
			->request('POST',
				$host,
				['headers'=>$header,
				'form_params'=> $informasiLos,
				]
			);
		}catch (RequestException $e){
			return  $e->getMessage();
		}
		//update resoinse status jadi pending
		$updateStatus = EForm::where('id',$eform_id)
		->update(['response_status'=>'pending']);

		$alamatDom = $request['PersonalAlamatDomisili'].' '.$request['PersonalAlamatDomisili2']
    	.' '.
    	$request['PersonalAlamatDomisili3'].', RT/RW '.$request['Rt'].'/'.$request['Rw'].', Kecamatan '.$request['Camat'];

    	//update data di eform
    	$update = EForm::where('id',$eform_id)->update([
    		'address'=>$alamatDom
    	]);

		$body = $res->getBody();
		$obj = json_decode($body);
		// $data = $obj->responseData;

		//update data user
		//get user  from eform
		$eformData = EForm::where('id',$eform_id)->first();
		$apregno = $request['appNumber'];

		//update eform response status
		// $updateStatus = EForm::where('id',$eform_id)
		// ->update(['response_status'=>'verified']);
		return response()->json($obj);

    }

    function sendFinishVerificationEmail(){
    	
    }

    function getApregnoFromKKDetails($eform_id){
    	$kk = KartuKredit::where('eform_id',$eform_id)->first();
   		$apRegno = $kk['appregno'];
    	return $apRegno;
    }

    public function cekDataNasabah($apRegno){
    	$host = '10.107.11.111:9975/api/dataNasabah';
    	$header = ['access_token'=> $this->tokenLos];
    	$client = new Client();

    	try{
    		$res = $client->request('POST',$host, ['headers' =>  $header,
    				'form_params' => ['apRegno' => $apRegno]
    			]);
    	}catch (RequestException $e){
    		return  $e->getMessage();
    	}

    	$body = $res->getBody();
    	$obj = json_decode($body);

    	return response()->json($obj);

    }

    function generateSmsCode(){
    	$code =mt_rand(102030, 999999);
    	return $code;
    }

    public function sendSMS(Request $req){
    	$pn = $req['handphone'];
    	$eformid = $req['eform_id'];
		$kk = KartuKredit::where('eform_id',$eformid)->first();
    	$apregno = $kk['appregno'];
    	$code = $this->generateSmsCode();
    	$message = 'Kode unik anda adalah '.$code.' . Periksa dan isi kode verifikasi pada field verifikasi yang kami sediakan pada email';

    	//save code ke kredit details
    	$updateCode = KartuKredit::where('appregno',$apregno)->update([
    		'verification_code'=>$code
    	]);

    	$host = '10.107.11.111:9975/notif/tosms';
    	$header = ['access_token'=> $this->tokenLos];
    	$client = new Client();

    	try{
    		$res = $client->request('POST',$host, ['headers' =>  $header,
    				'form_params' => ['handphone' => $pn,'message'=>$message]
    			]);
    	}catch (RequestException $e){
    		return  $e->getMessage();
    	}

    	$body = $res->getBody();
    	$obj = json_decode($body);

    	return response()->json([
    		'responseCode' => '00',
    		'contents' =>$obj
    	]);
    }

    public function toEmail(Request $req){
    	//email, subject, message
    	$email = $req['email'];
    	$eformid = $req['eform_id'];

    	// $message = $req['message'];
    	$kk = KartuKredit::where('eform_id',$eformid)->first();
    	$apregno = $kk['appregno'];

    	$dataKredit = KartuKredit::where('appregno',$apregno)->first();
    	$emailGenerator = new KreditEmailGenerator();
    	// $routes = 'apimybri.bri.co.id/api/v1/int/kk/verifyemail';
    	$routes = 'api.dev.net/api/v1/int/kk/verifyemail';
    	$message = $emailGenerator
    	->sendEmailVerification($dataKredit,$apregno,$routes);
    	\Log::info('======== data kredit =========');
   		\Log::info($dataKredit);
    	$host = '10.107.11.111:9975/notif/toemail';
    	$header = ['access_token'=> $this->tokenLos];
    	$client = new Client();

    	try{
    		$res = $client->request('POST',$host, ['headers' =>  $header,
    				'form_params' => ['email' => $email,'subject'=>'Email Verifikasi Pengajuan Kartu Kredit BRI','message'=>$message]
    			]);
    	}catch (RequestException $e){
    		return  $e->getMessage();
    	}

    	$body = $res->getBody();
    	$obj = json_decode($body);

    	return response()->json([
    		'responseCode' => '00',
    		'contents' =>$obj
    	]);
    }


    function verify($eform_id){
    	$updateStatus = EForm::where('id',$eform_id)
		->update(['response_status'=>'verified']);
		return true;
    }

    function isVerified($eform_id){
    	$ef = EForm::where('id',$eform_id)->first();
    	$ver = $ef['response_status'];
    	if($ver == 'verified'){
    		return true;
    	}else{
    		return false;
    	}
    }

     public function checkEmailVerification(Request $request){
     	$req = $request->all();
    	$codeVerif = $request->code;
    	$apRegno = $request->apregno;
    	$data = KartuKredit::where('appregno',$apRegno)->first();
    	$correctCode = $data['verification_code'];
    	$eformid = $data['eform_id'];

    	if($this->isVerified($eformid)){
    		return "Email telah diverifikasi";
    	}else{
    		if ($codeVerif == $correctCode){
    			//update ke eform
    			$updateEform = $this->verify($eformid);
    			$refNumber = EForm::where('id',$eformid)->first();
    			$refNumber = $refNumber['ref_number'];
    			$createQrcode = $this->createQrcode($refNumber);
    			return "Email telah tervirifikasi";
    		}else{
    			return response()->json([
    				'responseCode'=>'01',
    				'responseMessage'=>'Kode Salah'
    			]);
    		}
    	}
    }

    function createQrcode($refnumber){

    }

    public function analisaKK(Request $req){
    	$eformId = $req->eform_id;
    	$dataKredit = KartuKredit::where('eform_id',$eformId)->first();

    	$jenisNasabah = $dataKredit['jenis_nasabah'];

    	$apregno = $dataKredit['appregno'];
    	\Log::info('apregno = '.$apregno);
    	$dataLos = $this->cekDataNasabah($apregno);

    	$npwp = $dataKredit['image_npwp'];
    	$ktp = $dataKredit['image_ktp'];
    	$slipGaji = $dataKredit['image_slip_gaji'];

    	$scoring = $this->getScoring($apregno);

    	if ($jenisNasabah == 'debitur'){
    		
    		return response()->json([
    			'responseCode'=>'00',
    			'responseMessage'=>'sukses',
    			'contents'=>$dataKredit,
    			'images'=>[
    				'npwp'=>$npwp,
    				'ktp'=>$ktp,
    				'slip_gaji'=>$slipGaji
    			],
    			'data_los'=> $dataLos,
    			'score'=>$scoring
    		]);
    	}else{
    		$nametag = $dataKredit['image_nametag'];
    		$kartuBankLain = $dataKredit['image_kartu_bank_lain'];

    		return response()->json([
    			'responseCode'=>'00',
    			'responseMessage'=>'sukses',
    			'contents'=>$dataKredit,
    			'images'=>[
    				'npwp'=>$npwp,
    				'ktp'=>$ktp,
    				'slip_gaji'=>$slipGaji,
    				'nametag'=>$nametag,
    				'kartu_bank_lain'=>$kartuBankLain
    			],
    			'data_los'=> $dataLos,
    			'score'=>$scoring
    		]);
    	}

    	//eror
    	return response()->json([
    		'responseCode'=>'01',
    		'responseMessage'=>'terjadi kesalahan'
    	]);
    }

    function getScoring($apRegno){
    	$TOKEN_LOS = $this->tokenLos;
		$client = new Client();
		$host = '10.107.11.111:9975';

		try{
			$res = $client
			->request('POST',
				$host.'/api/scoring',
				['headers'=>['access_token'=> $TOKEN_LOS],
				'form_params'=> ['apRegno'=>$apRegno],
				]
			);
		}catch (RequestException $e){
			return response()->json([
				'responseCode'=>'99',
				'responseMessage'=>$e->getMessage()
			]);
		}
		
		$body = $res->getBody();
		$obj = json_decode($body);
		$data = $obj->responseData;

		return $data;
	}

	public function finishAnalisa(KreditRequest $req){
		$apregno = $req->apRegno;
		$eformId= $req->eform_id;
		$catRekAo = $req->catatanRekomendasiAO;
		$rekLimitKartu = $req->rekomendasiLimitKartu;
		$rangeLimit =  $req->range_limit;
		$losScore = $req->los_score;
		$losResult = $this->losScoreResult($losScore);

		if ($losResult == 'proceed'){
			$anStatus = 'analyzed';
		}else{
			$anStatus = 'rejected';
		}


		$dataKK = KartuKredit::where('appregno',$apregno)->first();
		$updateKK = KartuKredit::where('appregno',$apregno)->update([
			'is_analyzed'=>true,
			'catatan_rekomendasi_ao'=>$catRekAo,
			'rekomendasi_limit_kartu'=>$rekLimitKartu,
			'pilihan_kartu'=>$req->cardType,
			'range_limit'=>$rangeLimit,
			'los_score' =>$losScore,
			'analyzed_status'=>$anStatus
		]);

		 //lengkapi data kredit di eform
		$newData = [
			'range_limit'=>$rangeLimit,
			'is_analyzed'=> 'true',
			'los_score' =>$losScore,
			'analyzed_status'=>$anStatus
		];
		$jsonData = json_encode($newData);
        $eform = EForm::where('id',$eformId)->update([
            'kk_details'=>$jsonData
        ]);

		return response()->json([
			'responseCode'=>'00',
			'responseMessage'=>'analisa berhasil',
			'contents'=>$dataKK
		]);

	}

	function losScoreResult($score){
		if ($score >= '550'){
			return 'proceed';
		}else{
			return 'end';
		}
	}

	public function putusanPinca(KreditRequest $req){
		
		$apregno = $req->apRegno;
		$msg = $req->msg;
		$putusan = $req->putusan;
		$limit = $req->limit;

		$kk = new KartuKredit();
		$req = $req->all();
		
		if ($putusan == 'approved'){
			$host = $this->hostLos.'/api/approval';
			$data = $kk->createApprovedRequirements($req);
			$updateLimit = KartuKredit::where('appregno',$apregno)->update([
			'rekomendasi_limit_kartu'=>$limit
		]);

		}else{
			$host = $this->hostLos.'/api/reject';
			$data = $kk->createRejectedRequirements($req);
		}
		
		//kirim ke los.
		$client = new Client();
		try{
			$res = $client->request('POST',$host,
			['headers' => ['access_token'=>$this->tokenLos],
			'form_params'=> $data
			]
			);

		}catch(RequestException $e){
			return response()->json([
				'responseCode'=>'99',
				'responseMessage'=>$e->getMessage()
			]);	
		}

		
		//kirim ke db mybri
		$updateKK = KartuKredit::where('appregno',$apregno)->update([
			'approval'=>$putusan,
			'catatan_rekomendasi_pinca'=>$msg
		]);
		//tampilin ke eform
		$dataKK = KartuKredit::where('appregno',$apregno)->first();
		$eformId= $req->eform_id;
		$rangeLimit =  $dataKK->range_limit;
		$losScore = $dataKK->los_score;
		$anStatus = $dataKK->analyzed_status;


		$newData = [
			'range_limit'=>$rangeLimit,
			'is_analyzed'=> 'true',
			'los_score' =>$losScore,
			'analyzed_status'=>$anStatus,
			'approval'=>$putusan
		];

		$jsonData = json_encode($newData);
        $eform = EForm::where('id',$eformId)->update([
            'kk_details'=>$jsonData
        ]);

		$eformId = $req['eform_id'];
		$updateEform = EForm::where('id',$eformId)->update([
			'is_approved'=>true
		]);

		

		$body = $res->getBody();
    	$obj = json_decode($body);

    	return response()->json([
    		'responseCode'=>'00',
    		'responseMessage'=>'Success',
    		'contents'=>$obj
    	]);


	}

    public function listReject(){
    	$header = ['access_token'=> $this->tokenLos];
    	$client = new Client();
			 try{
                $res = $client->request('POST',$this->hostLos.'/api/listreject', 
                	['headers' =>  $header
                    ]);
            }catch (RequestException $e){
                return response()->json([
                    'responseCode'=>'99',
                    'responseMessage'=> $e->getMessage()
                ]);
            }

            $body = $res->getBody();
			$obj = json_decode($body);

			if ($obj->responseCode == 0){
				$data = $obj->responseData;
				return response()->json([
					'responseCode'=>'00',
					'responseMessage'=>'Success',
					'contents' => $data
				]);
			}
			
    }

  

}

