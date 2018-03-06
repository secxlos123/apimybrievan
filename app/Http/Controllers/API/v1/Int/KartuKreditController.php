<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\v1\EFormController;
use App\Http\Requests\API\v1\EFormRequest;
use App\Models\KartuKredit;
use GuzzleHttp\Client;
use App\Models\UserServices;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Asmx;

class KartuKreditController extends Controller{



	public $hostLos = '10.107.11.111:9975';
	public $tokenLos = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJsb3NhcHAiLCJhY2Nlc3MiOlsidGVzIl0sImp0aSI6IjhjNDNlMDNkLTk5YzctNDJhMC1hZDExLTgxODUzNDExMWNjNCIsImlhdCI6MTUxODY2NDUzOCwiZXhwIjoxNjA0OTc4MTM4fQ.ocz_X3duzyRkjriNg0nXtpXDj9vfCX8qUiUwLl1c_Yo';
	
	public $hostPefindo = '10.35.65.167:6969';

	// public function __construct(User $user, UserServices $userservices, UserNotification $userNotification)
 //    {
 //        $this->userServices = new UserServices;
 //        $this->user = $user;
 //        $this->userservices = $userservices;
 //        $this->userNotification = $userNotification;
 //    }

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
		// $data = $obj->responseData;

		return response()->json($obj);
    }

    public function updateDataLos(Request $req){
    	$header = ['access_token'=> $this->tokenLos];
    	$host = '10.107.11.111:9975/api/updateData';
    	$client = new Client();

    	$kk = new KartuKredit();
    	$informasiLos = $kk->convertToAddDataLosFormat($req,'update');

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

		return response()->json($obj);

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
    	$message = 'Kode unik anda adalah '.['message'].'\. periksa email';

    	$host = '10.107.11.111:9975/notif/tosms';
    	$client = new Client();
    }

    public function toEmail(Request $req){
    	
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

    public function eform(EFormRequest $req){
    	$kk = new KartuKredit();
    	$eform = $kk->createEform($req);

    	return $eform;
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

}

 