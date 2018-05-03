<?php

namespace App\Http\Controllers\API\v1;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Sentinel;
use DB;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use RestwsHc;
use App\Models\ApiPdmTokensBriguna;
use Cache;

class SchedulerMitraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
			
	private function fetch()
    {
        $long = number_format('106.86758', 5);
        $lat = number_format('-6.232423', 5);
        $return = RestwsHc::setBody([
            'request' => json_encode([
                'requestMethod' => 'get_near_branch_v2',
                'requestData'   => [
                    'app_id' => 'mybriapi',
                    'kode_branch' => "0",
                    'distance'    => "1000000000000000000",

                    // if request latitude and longitude not present default latitude and longitude cimahi
                    'latitude'  => $lat,
                    'longitude' => $long
                ]
            ])
        ])
        ->post('form_params');
        return $return;
    }
	
	public function ListBranch($data, $token)
    {
		 $host = env('APP_URL');
		 
      if($host == 'http://api.dev.net/' || $host == 'http://103.63.96.167/api/' || $host=='https://apimybridev.bri.co.id/'){
		$urls = 'http://10.35.65.208:81/';
	}else{
		$urls = 'http://api.briconnect.bri.co.id/';  
	 }
      $client = new Client();
	$requestListExisting = $client->request('GET', $urls.'bribranch/region/v3',
				[
				  'headers' =>
				  [
					'Authorization' => 'Bearer '.$token
				  ]
				]
			  );
	  $listExisting = json_decode($requestListExisting->getBody()->getContents(), true);

      return $listExisting;
    }
	
	public function kanwilsss(){
	  if ( count(ApiPdmTokensBriguna::all()) > 0 ) {
        $apiPdmToken = ApiPdmTokensBriguna::latest('id')->first()->toArray();
      } else {
        $this->gen_token_briguna();
        $apiPdmToken = ApiPdmTokensBriguna::latest('id')->first()->toArray();
      }
      if ($apiPdmToken['expires_in'] >= date("Y-m-d H:i:s")) {
        $token = $apiPdmToken['access_token'];
        $listExisting = $this->ListBranch($token);

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $listExisting
        ]);
      } else {
        $briConnect = $this->gen_token_briguna();
        $apiPdmToken = ApiPdmTokensBriguna::latest('id')->first()->toArray();
        
        $token = $apiPdmToken['access_token'];
        $listExisting = $this->ListBranch($token);
		return $listExisting;
	}
	}
	public function scheduler( Request $request )
	{
		
		try{
			$kanwilsss = $this->kanwilsss();
			print_r($kanwilss);die();
			if($kanwilsss['responseCode']=='00'){
				
			}
		}catch(Exception $e){
			$logsql = DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'".$time_first."',localtime,'".$e."')");
			print_r($e);
			die();
		}
		try{
		    $UKERS = $this->fetch();
			if($UKERS['responseCode']=='00'){
				 DB::statement('CREATE TABLE uker_table_create(
					   "id" text,
					   "unit_kerja" text,
					   "unit_induk" text,
					   "kanca_induk" text,
					   "jenis_uker" text,
					   "kode_uker" text,
					   "dati2" text,
					   "dati1" text,
					   "alamat" text,
					   "no_telp" text,
					   "no_fax" text,
					   "koordinat" text,
					   "latitude" float8,
					   "longitude" float8
					);');
					
					$UKERS = $UKERS['responseData'];
					foreach($UKERS as $uker){
							$id = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$uker['id']));
							$unit_kerja = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$uker['unit_kerja']));
							$unit_induk = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$uker['unit_induk']));
							$kanca_induk = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$uker['kanca_induk']));
							$jenis_uker = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$uker['jenis_uker']));
							$kode_uker = $uker['kode_uker'];
							$dati2 = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$uker['dati2']));
							$dati1 = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$uker['dati1']));
							$alamat = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$uker['alamat']));
							$no_telp = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$uker['no_telp']));
							$no_fax = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$uker['no_fax']));
							$koordinat = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$uker['koordinat']));
							$latitude = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$uker['latitude']));
							$longitude = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$uker['longitude']));
							
							DB::statement("INSERT INTO uker_table_create VALUES('".$id."','".$unit_kerja."','".$unit_induk."','"
											.$kanca_induk."','".$jenis_uker."','".$kode_uker."','"
											.$dati2."','".$dati1."','".$alamat."','"
											.$no_telp."','".$no_fax."','".$koordinat."','"
											.$latitude."','".$longitude."');");
					}
					DB::statement("ALTER TABLE uker_tables RENAME TO uker_tablesxxx;");
							DB::statement("ALTER TABLE uker_table_create RENAME TO uker_tables;");
							DB::statement("DROP TABLE uker_tablesxxx;");
			}
		}catch(Exception $e){
			$logsql = DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'".$time_first."',localtime,'".$e."')");
			print_r($e);
			die();
		}
		if($request->all()){
			$paginates = $request->all();
			$paginates = $paginates['page'];
		}else{
			$paginates = 0;
		}
		ini_set('max_execution_time', 12000);
		error_reporting(E_ALL);
		$time_first = date('H:i:s');
		$datenow = date('Y/M/D');
		
		//----------prod-----------------------
				$servernyalas = '';
			  $host = env('APP_URL');
			  if($host == 'http://api.dev.net/' || $host == 'http://103.63.96.167/api/'){	
					$servernyalas = 'sqlsrv';
			}else{
					$servernyalas = 'sqlsrv_prod';
			  }
		//---------------------------------
		// Create connection
	
		//$conn = mysqli_connect($servername, $username, $password);
		$datalas = array();
		\Log::info("-------------------connect to las-----------------");
		try{
							$datalas = DB::connection($servernyalas)->table('LAS_M_INSTANSI_BRI')->select()->paginate(100000);
							
							//$datalas_encode = json_decode(json_encode($datalas), True);
							$last_page = $datalas->lastPage();
							$current_page = $datalas->currentPage();
							$nextPage = (int)$current_page + 1;
							$url = env('APP_URL').'scheduler_mitra';
							$next_page_url = $url.'?page='.$nextPage;
							$isi_data = '0';
							if($datalas->items()[0]->ID_INSTANSI_BRI != ""){
								$isi_data = '1';
							}
							
//							return $datalas_encode;die();
		}catch(Exception $e){
			$logsql = DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'".$time_first."',localtime,'".$e."')");
			print_r($e);
			die();
		}
		
			\Log::info("-------------------GET ALL DATA LAS SUKSES-----------------");
			
		if($paginates==0){
			try{
				$logsql = DB::statement('CREATE TABLE mitra_create(
					   "idMitrakerja" text,
					   "NAMA_INSTANSI" text,
					   kode text,
					   "NPL" text,
					   "BRANCH_CODE" text,
					   "Jumlah_pegawai" text,
					   "JENIS_INSTANSI" text,
					   "UNIT_KERJA" text,
					   "Scoring" text,
					   "KET_Scoring" text,
					   jenis_bidang_usaha text,
					   alamat_instansi text,
					   alamat_instansi3 text,
					   telephone_instansi text,
					   rating_instansi text,
					   lembaga_pemeringkat text,
					   tanggal_pemeringkat text,
					   go_public text,
					   no_ijin_prinsip text,
					   date_updated text,
					   updated_by text,
					   acc_type text,
					   alamat_instansi2 text
					);');
			}catch(Exception $e){

				\Log::info("-------------------ERROR CREATE mitra MYBRI-----------------");
				 DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'".$time_first."',localtime,'".$e."')");
				\Log::info($e);
				print_r($e);
				die();
			}
		}
		
				\Log::info("-------------------CREATE TABLE MITRA NEW SUKSES-----------------");
					$sql = "";
					$query = "";
				foreach ($datalas->items() as $data) {
					//,STR_TO_DATE('$data[3]','%Y%m%d')
				try{
					//to_number('".$data['KODE_UKER_PEMRAKARSA']."','99G999D9S')
					
					$idinstansi = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->ID_INSTANSI_BRI));
					$namainstansi = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->NAMA_INSTANSI));
					$kodeinstansi = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->KODE_INSTANSI));
					$posisinpl = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->POSISI_NPL));
					$kodeuker = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->KODE_UKER_PEMRAKARSA));
					$jumlahkaryawan = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->JUMLAH_KARYAWAN));
					$jenisinstansi = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->JENIS_INSTANSI));
					$jenisbidangusaha = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->JENIS_BIDANG_USAHA));
					$telponinstansi = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->TELEPON_INSTANSI));
					$lembagapemeringkat = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->LEMBAGA_PEMERINGKAT));
					$tglpemeringkat = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->TANGGAL_PEMERINGKAT));
					$gopublic = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->GO_PUBLIC));
					$noijinprinsip = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->NO_IJIN_PRINSIP));
					$dateupdate = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->DATE_UPDATED));
					$updateby = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->UPDATED_BY));
					$acctype = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->ACC_TYPE));
					$alamatinstansi = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->ALAMAT_INSTANSI));
					$alamatinstansi2 = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->ALAMAT_INSTANSI2));
					$alamatinstansi3 = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->ALAMAT_INSTANSI3));
					$idinstansibri = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->ID_INSTANSI_BRI));
					$telephone_instansi_bri = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->TELEPON_INSTANSI));
					$rating_instansi_bri = iconv("UTF-8", "UTF-8//IGNORE",str_replace("'","",$data->RATING_INSTANSI));
					
					if(strlen($kode_uker)=='5'){
						$kode_uker = $kode_uker;
					}else{
						$branchut = '';
						$o = strlen($kode_uker);
										$branchut = '';
										for($y=$o;$y<5;$y++){
											if($y==$o){
												$branchut = '0'.$kode_uker;
											}else{
												$branchut = '0'.$branchut;
											}
										} 
										$kode_uker = $branchut;	
					}
					$sql .= DB::statement("INSERT INTO mitra_create VALUES('".$idinstansibri."','".$namainstansi."','".$kodeinstansi."',
										'".$posisinpl."','".$kodeuker."',
					'".$jumlahkaryawan."','".$jenisinstansi."','','70','','".$jenisbidangusaha."',
					'".$alamatinstansi."','".$alamatinstansi3."','".$telephone_instansi_bri."','".$rating_instansi_bri."',
					'".$lembagapemeringkat."','".$tglpemeringkat."','".$gopublic."',
					'".$noijinprinsip."','".$dateupdate."','".$updateby."','".$acctype."','".$alamatinstansi2."');");
				}catch(Exception $e) {
					\Log::info("-------------------ERROR LOG Insert mitra MYBRI-----------------");
					DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'".$time_first."',localtime,'".$e."')");
					\Log::info($e);
					print_r($e);
					die();
				}
						/* $sql = "INSERT INTO $table VALUES('$data[0]','$data[1]','$data[2]','$data[11]',to_number('$data[14]','99G999D9S'),
					'$data[17]','$data[18]','','70','','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[12]',
					'$data[13]','$data[15]','$data[16]','$data[19],'$data[20]'')"; */
					
				}
				\Log::info("-------------------INSERT MITRA SUKSES ".$paginates."-----------------");
				\Log::info($sql);
				
				if($current_page!=$last_page){
							//$a = explode('/',$next_page_url);
							header("Location:scheduler_mitra?page=".$nextPage);die();
				}else{
					try{
					if($isi_data == '1'){
							DB::statement("ALTER TABLE mitra RENAME TO mitraxxx;");
							DB::statement("ALTER TABLE mitra_create RENAME TO mitra;");
							DB::statement("DROP TABLE mitraxxx;");
							DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'".$time_first."',localtime,'Sukses')");
							\Log::info("-------------------Sukses ALL-----------------");
							\Log::info('SUKSES');
							print_r('SUKSES');
							die();
						
					}else{
							DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'".$time_first."',localtime,'Data Ketarik Kosong')");
							print_r('GAGAL');
							die();
					}
					}catch(Exception $e) {
						\Log::info("-------------------ERROR LOG Insert mitra MYBRI-----------------");
						DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'".$time_first."',localtime,'".$e."')");
						print_r($e);
						die();
					}
				}
				
			
	}
   
}

