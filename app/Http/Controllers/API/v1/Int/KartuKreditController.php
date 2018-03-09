<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\v1\EFormController;
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

use App\Http\Controllers\API\v1\Int\KreditEmailGeneratorController;

class KartuKreditController extends Controller{



	public $hostLos = '10.107.11.111:9975';
	public $tokenLos = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJsb3NhcHAiLCJhY2Nlc3MiOlsidGVzIl0sImp0aSI6IjhjNDNlMDNkLTk5YzctNDJhMC1hZDExLTgxODUzNDExMWNjNCIsImlhdCI6MTUxODY2NDUzOCwiZXhwIjoxNjA0OTc4MTM4fQ.ocz_X3duzyRkjriNg0nXtpXDj9vfCX8qUiUwLl1c_Yo';
	
	public $hostPefindo = '10.35.65.167:6969';


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

	public function getAllInformation(){
		$TOKEN_LOS = $this->tokenLos;
		$client = new Client();
		$host = '10.107.11.111:9975';

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

      	 	return  $e->getMessage();
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
			echo "Terjadi kesalahan";
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

    public function updateDataLos(Request $req){
    	//saat verifikasi
    	$header = ['access_token'=> $this->tokenLos];
    	$host = '10.107.11.111:9975/api/updateData';
    	$client = new Client();
    	
    	$request = $req->all();
    	$eform_id = $request['eform_id'];
    	$request['apregno'] = $this->getApregnoFromKKDetails($eform_id);

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

		$body = $res->getBody();
		$obj = json_decode($body);
		// $data = $obj->responseData;

		//update data user
		//get user id from eform
		$eformData = EForm::where('id',$eform_id)->first();
		$apregno = $request['apregno'];

		//update eform response status
		$updateStatus = EForm::where('id',$eform_id)
		->update(['response_status'=>'verified']);
		

		$updatedData = $this->updateUserTable($apregno);

		// $updateLos = User::where('id',$eform_id)
		// ->update('');


		return response()->json($obj);

    }
    function updateUserTable($apregno){
    	//ganti semua user
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

    public function sendSMS(Request $req){
    	$pn = $req['handphone'];
    	$message = 'Kode unik anda adalah '.$req['message'].'\. periksa email';

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
    		'responseCode' => '01',
    		'contents' =>$obj
    	]);
    }

    public function toEmail(Request $req){
    	//email, subject, message
    	$email = $req['email'];
    	$subject = $req['subject'];
    	// $message = $req['message'];

    	$apregno = $req['apRegno'];

    	$dataKredit = KartuKredit::where('appregno',$apRegno)->first();
    	$emailGenerator = new KreditEmailGeneratorController();
    	$message = $emailGenerator->sendEmailVerification($dataKredit,$apregno,'www.google.com');

    	$host = '10.107.11.111:9975/notif/toemail';
    	$header = ['access_token'=> $this->tokenLos];
    	$client = new Client();

    	try{
    		$res = $client->request('POST',$host, ['headers' =>  $header,
    				'form_params' => ['email' => $email,'$subject'=>$subject,'message'=>$message]
    			]);
    	}catch (RequestException $e){
    		return  $e->getMessage();
    	}

    	$body = $res->getBody();
    	$obj = json_decode($body);

    	return response()->json([
    		'responseCode' => '01',
    		'contents' =>$obj
    	]);
    }

    public function checkDedup($nik){
    	// $nik = $req['nik'];
    	$host = '10.107.11.111:9975/api/nik';
    	$header = ['access_token'=> $this->tokenLos];
    	$client = new Client();

    	try{
    		$res = $client->request('POST',$host, ['headers' =>  $header,
    				'form_params' => ['nik' => $nik]
    			]);
    	}catch (RequestException $e){
    		return  $e->getMessage();
    	}

    	$body = $res->getBody();
    	$obj = json_decode($body);
    	$responseCode = $obj->responseCode;

    	if ($responseCode == 0 || $responseCode == 00){
    		//langsung merah. update eform.
    		return response()->json([
    			'responseCode' => 01,
    			'responseMessage' => 'Nasabah pernah mengajukan kartu kredit 6 bulan terakhir'
    		]);
    	}
    }

    public function analisaKK(Request $req){
    	$eformId = $req->eform_id;
    	$dataEform = KartuKredit::where('eform_id',$eformId)->first();

    	$jenisNasabah = $dataEform['jenis_nasabah'];

    	$apregno = $dataEform['appregno'];
    	\Log::info('apregno = '.$apregno);
    	$dataLos = $this->cekDataNasabah($apregno);

    	$npwp = $dataEform['image_npwp'];
    	$ktp = $dataEform['image_ktp'];
    	$slipGaji = $dataEform['image_slip_gaji'];

    	$scoring = $this->getScoring($apregno);

    	if ($jenisNasabah == 'debitur'){
    		
    		return response()->json([
    			'responseCode'=>'00',
    			'responseMessage'=>'sukses',
    			'images'=>[
    				'npwp'=>$npwp,
    				'ktp'=>$ktp,
    				'slip_gaji'=>$slipGaji
    			],
    			'data_los'=> $dataLos,
    			'score'=>$scoring
    		]);
    	}else{
    		$nametag = $dataEform['image_nametag'];
    		$kartuBankLain = $dataEform['image_kartu_bank_lain'];

    		return response()->json([
    			'responseCode'=>'00',
    			'responseMessage'=>'sukses',
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
			echo "Terjadi kesalahan";
			return  $e->getMessage();
		}

		$body = $res->getBody();
		$obj = json_decode($body);
		$data = $obj->responseData;

		return $data;

	}

    public function pefindo(){
    	$getPefindo = Asmx::setEndpoint( 'SmartSearchIndividual' )
                ->setBody([
                    'Request' => json_encode( array(
                        'nomer_id_pefindo' => '3312123007890001'
                        , 'nama_pefindo' => 'YOGA HERAWAN'
                        , 'tanggal_lahir_pefindo' => '30-07-1989'
                        , 'alasan_pefindo' => 'tes dev kartu kredit'
                    ) )
                ])
                ->post( 'form_params' );
    }



    public function checkEmailVerification(Request $request){
    	$codeVerif = $request->code;
    	$apRegno = $request->apRegno;
    	$data = KartuKredit::where('appregno',$apRegno)->first();
    	$correctCode = $data['verification_code'];

    	if ($codeVerif == $correctCode){
    		return response()->json([
    			'responseCode'=>'00',
    			'responseMessage'=>'Kode Benar'
    		]);
    	}else{
    		return response()->json([
    			'responseCode'=>'01',
    			'responseMessage'=>'Kode Salah'
    		]);
    	}
    }

}

 