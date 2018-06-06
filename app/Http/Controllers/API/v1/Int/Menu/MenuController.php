<?php

namespace App\Http\Controllers\API\v1\Int\Menu;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Models\Mitra3;
use Sentinel;
use DB;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Brispot;
use Cache;
use App\Models\Crm\apiPdmToken;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
		function aasort (&$array, $key) {
			$sorter=array();
			$ret=array();
			reset($array);
			foreach ($array as $ii => $va) {
				$sorter[$ii]=$va[$key];
			}
			asort($sorter);
			foreach ($sorter as $ii => $va) {
				$ret[$ii]=$array[$ii];
			}
			$array=$ret;
		  return response()->success( [
            'message' => 'Sukses',
            'contents' => $return
        ]);
		}
	public function createNew(Request $request ){
		try{
		exec('php artisan make:migration create_table_'.$request->view.' --table=et_'.$request->view.'_tbl');
		DB::table('tbl_menus')->insert(
			['view' => $request->view]
		);
		$dir = dirname(__FILE__);
		$dirup = str_replace('\menu','',$dir);
		$directory = $dirup.'\\Et\\'.$request->view;
		exec('mkdir '.$directory);
		exec('cp '.$dirup.'\\CopyController.php'.' '.$directory.'\\'.$request->view.'Controller.php';
		 return response()->success( [
            'message' => 'Sukses',
        ]);
		}catch(Exception $e){
			return ['code'=>'433','description'=>'gagal','contents'=>''];
		}
	}
	public function getView( Request $request )
	{
			  $return = DB::table('view_table')
							 ->select('view')
							 ->groupBy('view')
							 ->get();
					$return = $return->toArray();
					$return = json_decode(json_encode($return), True);

		if(empty($return)){
			return ['code'=>'433','description'=>'gagal','contents'=>''];
		}else{

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $return
        ]);
		}
	}


}
