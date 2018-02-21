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
		//----------prod-----------------------
				$servername = '';
				$servernamelas = '';
				$username = 'administrator'
				$usernamelas = 'sa';
				$password = 'P@ssw0rd';
				$passwordlas = 'starbuck';

			  $host = env('APP_URL');
			  if($host == 'http://api.dev.net/'){		
				$servername = '10.35.65.156:5432';
				$servernamelas = "10.35.65.166"
				$database = "mybri";
			}else{
				$servername = '172.18.45.22';
				$servernamelas = = "172.21.53.70";
				$database = "mybri_prod";
			  }
		//---------------------------------
		// Create connection
		$conn = mysqli_connect($servername, $username, $password);
		$connlas = mysqli_connect($servernamelas, $usernamelas, $passwordlas);
		$table = 'mitra';
		//select a database to work with
		$selected = mysqli_select_db($conn,$database);
		$selectedlas = mysqli_select_db($connlas,"LAS");
		$query = "SELECT * FROM LAS_M_INSTANSI_BRI ORDER BY ID_INSTANSI_BRI ASC;";
		$datalas = mysqli_query($conn,$query);
			
			foreach ($datalas as $data) {
				//,STR_TO_DATE('$data[3]','%Y%m%d')
				$sql = "";
					$sql = "INSERT INTO $table VALUES('$data[0]','$data[1]','$data[2]','$data[11]',to_number('$data[14]','99G999D9S'),
				'$data[17]','$data[18]','','70','','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$data[12]',
				'$data[13]','$data[15]','$data[16]','$data[19],'$data[20]'')";

				if($data[1] != "" && $sql != ""){
					if(mysqli_query($conn,$sql)){
						$logsql = "INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'$time_first',localtime,'success')";
						mysqli_query($conn,$logsql);
						echo "clear<br>";
					}else{
						$logsql = "INSERT INTO log_mitra VALUES((select count(*)+1 from log_mitra),now(),'$time_first',localtime,'$sql')";
						mysqli_query($conn,$logsql);
						echo "gagal : $sql<br>";
					}
				}		
			}
			mysqli_close($conn);
	}
   
}

