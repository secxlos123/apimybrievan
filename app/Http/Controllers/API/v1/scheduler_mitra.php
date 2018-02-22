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
		ini_set('max_execution_time', 12000);
		error_reporting(E_ALL);
		$time_first = date('H:i:s');
		$datenow = date('Y/M/D');
		//----------prod-----------------------
				$servername = '';
				$servernamelas = '';
				$conn_array_las = array (
					"UID" => "sa",
					"PWD" => "starbuck",
					"Database" => "LAS",
				);

			  $host = env('APP_URL');
			  if($host == 'http://api.dev.net/'){		
				$servername = '10.35.65.156:5432';
				$servernamelas = "10.35.65.166";
				$database = "mybri";
			}else{
				$servername = '172.18.45.22';
				$servernamelas = "172.21.53.70";
				$database = "mybri_prod";
			  }
		//---------------------------------
		// Create connection
	
		//$conn = mysqli_connect($servername, $username, $password);
		\Log::info("-------------------connect to las-----------------");
				
		if(!sqlsrv_connect($servernamelas, $usernamelas, $passwordlas)){
			\Log::info("-------------------ERROR LOG TO LAS-----------------");
			$logsql = DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'$time_first',localtime,'Can't connect To LAS')");
			\Log::info($logsql);
			die("Can't connect To LAS");
		}
		
		try{
			$connlas = sqlsrv_connect($servernamelas, $conn_array_las);
		}catch(Exception $e) {
			\Log::info("-------------------ERROR LOG TO LAS-----------------");
				DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'$time_first',localtime,'".$e."')");
			\Log::info($e);
			die($e);
		}
		
			\Log::info("-------------------CONNECT LAS SUKSES-----------------");
		//$table = 'mitra';
		//select a database to work with
		//$selected = mysqli_select_db($conn,$database);
//		$selectedlas = mysqli_select_db($connlas,"LAS");
		$query = "SELECT * FROM LAS_M_INSTANSI_BRI ORDER BY ID_INSTANSI_BRI ASC;";
		$datalas = array();
		try{
			$datalas = sqlsrv_query($connlas,$query);
		}catch{
			\Log::info("-------------------ERROR CALL DATA LAS-----------------");
				DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'$time_first',localtime,'".$e."')");
			\Log::info($e);
			die($e);
		}
			\Log::info("-------------------GET ALL DATA LAS SUKSES-----------------");
			
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
				 DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'$time_first',localtime,'".$e."')");
				\Log::info($e);
				die($e);
			}
			
				\Log::info("-------------------CREATE TABLE MITRA NEW SUKSES-----------------");
					$sql = "";
				foreach ($datalas as $data) {
					//,STR_TO_DATE('$data[3]','%Y%m%d')
				try{
					$sql = DB::statement("INSERT INTO mitra VALUES('$data[0]','$data[1]','$data[2]','$data[11]',to_number('$data[14]','99G999D9S'),
					'$data[17]','$data[18]','','70','','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[12]',
					'$data[13]','$data[15]','$data[16]','$data[19],'$data[20]')");
				}catch(Exception $e) {
					\Log::info("-------------------ERROR LOG Insert mitra MYBRI-----------------");
					DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'$time_first',localtime,'".$e."')");
					\Log::info($e);
					die($e);
				}
				
				\Log::info("-------------------INSERT MITRA SUKSES-----------------");
				\Log::info($sql);
						/* $sql = "INSERT INTO $table VALUES('$data[0]','$data[1]','$data[2]','$data[11]',to_number('$data[14]','99G999D9S'),
					'$data[17]','$data[18]','','70','','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[12]',
					'$data[13]','$data[15]','$data[16]','$data[19],'$data[20]'')"; */
					
					
					try{
					if($data[1] != "" && $sql != ""){
							DB::statement("ALTER TABLE mitra RENAME TO mitraxxx;");
							DB::statement("ALTER TABLE mitra_create RENAME TO mitra;");
							DB::statement("DROP TABLE mitraxxx;");
							DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'$time_first',localtime,'Sukses')");
							\Log::info("-------------------Sukses ALL-----------------");
							\Log::info('SUKSES');
							die("clear");
						
					}else{
							DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'$time_first',localtime,'Data Ketarik Kosong')");
							\Log::info($e);
							die("Gagal");
					}
					}catch(Exception $e) {
						\Log::info("-------------------ERROR LOG Insert mitra MYBRI-----------------");
						DB::statement("INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'$time_first',localtime,'".$e."')");
						die($e);
					}
				}
			mysqli_close($conn);
	}
   
}

