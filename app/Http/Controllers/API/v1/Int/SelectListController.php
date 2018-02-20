<?php

namespace App\Http\Controllers\API\v1\Int;


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

class SelectListController extends Controller
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
		return $array;
		}

	public function getFasilitas( Request $request )
	{
			  $return = DB::table('mitra_detail_fasilitas_perbankan')
							 ->select('fasilitas_lainnya','id')
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
