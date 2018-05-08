<?php

namespace App\Http\Controllers\API\v1\eks;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Mitra;
use App\Models\Mitra4;
use App\Models\Mitra3;
use Sentinel;
use DB;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use RestwsHc;
use Cache;

class SelectCabangController extends Controller
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
	
	public function eksternal( Request $request )
	{
	        \Log::info($request->all());
				
			$limit = $request->input( 'limit' ) ?: 10;
			$mitra = Mitra4::filter( $request )->paginate($limit);
			//$mitra = $mitra->toArray();
        return response()->success([
            'contents' => $mitra,
            'message' => 'Sukses'
        ]);
	}
  
	


}
