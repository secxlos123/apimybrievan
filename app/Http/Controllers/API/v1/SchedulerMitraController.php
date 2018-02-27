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
use Cache;

class SchedulerMitraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
		
	public function scheduler( Request $request )
	{
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
			  if($host == 'http://api.dev.net/' || $host='http://localhost'){	
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
							$datalas = DB::connection($servernyalas)->table('LAS_M_INSTANSI_BRI')->select()->paginate(1000);
							
							//$datalas_encode = json_decode(json_encode($datalas), True);
							$last_page = $datalas->lastPage();
							$current_page = $datalas->currentPage();
							$path = $datalas->path;
							$nextPage = (int)$currentPage + 1;
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
				$logsql = DB::statement("CREATE TABLE mitra_create(
					   idMitrakerja text,
					   NAMA_INSTANSI text,
					   kode text,
					   NPL text,
					   BRANCH_CODE text,
					   Jumlah_pegawai text,
					   JENIS_INSTANSI text,
					   UNIT_KERJA text,
					   Scoring text,
					   KET_Scoring text,
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
					);");
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
					
					$idinstansi = str_replace("'","",$data->ID_INSTANSI_BRI);
					$namainstansi = str_replace("'","",$data->NAMA_INSTANSI);
					$kodeinstansi = str_replace("'","",$data->KODE_INSTANSI);
					$posisinpl = str_replace("'","",$data->POSISI_NPL);
					$kodeuker = str_replace("'","",$data->KODE_UKER_PEMRAKARSA);
					$jumlahkaryawan = str_replace("'","",$data->JUMLAH_KARYAWAN);
					$jenisinstansi = str_replace("'","",$data->JENIS_INSTANSI);
					$jenisbidangusaha = str_replace("'","",$data->JENIS_BIDANG_USAHA);
					$telponinstansi = str_replace("'","",$data->TELEPON_INSTANSI);
					$lembagapemeringkat = str_replace("'","",$data->LEMBAGA_PEMERINGKAT);
					$tglpemeringkat = str_replace("'","",$data->TANGGAL_PEMERINGKAT);
					$gopublic = str_replace("'","",$data->GO_PUBLIC);
					$noijinprinsip = str_replace("'","",$data->NO_IJIN_PRINSIP);
					$dateupdate = str_replace("'","",$data->DATE_UPDATED);
					$updateby = str_replace("'","",$data->UPDATED_BY);
					$acctype = str_replace("'","",$data->ACC_TYPE);
					$alamatinstansi = str_replace("'","",$data->ALAMAT_INSTANSI);
					$alamatinstansi2 = str_replace("'","",$data->ALAMAT_INSTANSI2);
					$alamatinstansi3 = str_replace("'","",$data->ALAMAT_INSTANSI3);
					
					$sql .= DB::statement("INSERT INTO mitra_create VALUES('".$data->ID_INSTANSI_BRI."','".$namainstansi."','".$kodeinstansi."',
										'".$posisinpl."','".$kodeuker."',
					'".$jumlahkaryawan."','".$jenisinstansi."','','70','','".$jenisbidangusaha."',
					'".$alamatinstansi."','".$alamatinstansi3."','".$data->TELEPON_INSTANSI."','".$data->RATING_INSTANSI."',
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
							$a = explode('/',$next_page_url);
							header("Location:".$a[5]);die();
				}else{
					try{
					if($isi_data == '1'){
							DB::statement("ALTER TABLE mitra_scheduller RENAME TO mitraxxx;");
							DB::statement("ALTER TABLE mitra_create RENAME TO mitra_scheduller;");
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

