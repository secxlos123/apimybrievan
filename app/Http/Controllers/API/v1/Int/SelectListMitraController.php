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

class SelectListMitraController extends Controller
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
	public function getMitra( Request $request )
	{
		if($request->keys=='induknonusaha'){
			
			  $return = DB::table('jenis_mitra_kerjasama')
							 ->select('NAMA','KODE')
							 ->where('jenis_mitra_kerjasama.JNS_BDAN_USAHA', '2')
							 ->get();
					$return = $return->toArray();
					$return = json_decode(json_encode($return), True);
				
		}elseif($request->keys=='indukbadanusaha'){
			$return = DB::table('jenis_mitra_kerjasama')
							 ->select('NAMA','KODE')
							 ->where('jenis_mitra_kerjasama.JNS_BDAN_USAHA', '1')
							 ->get();
					$return = $return->toArray();
					$return = json_decode(json_encode($return), True);
					
		}elseif($request->keys=='induk'){
			$DBASE = DB::table('jenis_mitra_kerjasama')
							 ->select('DBASE')
							 ->where('jenis_mitra_kerjasama.KODE', $request->data)
							 ->get();
					$DBASE = $DBASE->toArray();
					$DBASE = json_decode(json_encode($DBASE), True);
			$return = DB::table($DBASE[0]['DBASE'])
							 ->select('NAMA','KODE_2 AS KODE')
							 ->where($DBASE[0]['DBASE'].'.KODE', $request->data)
							 ->get();
					$return = $return->toArray();
					$return = json_decode(json_encode($return), True);
		}elseif($request->keys=='wilayah'){
			$keydata = (explode(".",$request->data));
			$DBASE = DB::table('jenis_mitra_kerjasama')
							 ->select('DBASE')
							 ->where('jenis_mitra_kerjasama.KODE', $keydata[0].'.'.$keydata[1])
							 ->get();
					$DBASE = $DBASE->toArray();
					$DBASE = json_decode(json_encode($DBASE), True);
			$return = DB::table($DBASE[0]['DBASE'].'_tingkat2')
							 ->select('NAMA','KODE_TINGKAT3 AS KODE')
							 ->where($DBASE[0]['DBASE'].'_tingkat2.KODE_TINGKAT2', $request->data)
							 ->get();
					$return = $return->toArray();
					$return = json_decode(json_encode($return), True);
		}elseif($request->keys=='kabupaten'){
			$keydata = (explode(".",$request->data));
			$DBASE = DB::table('jenis_mitra_kerjasama')
							 ->select('DBASE')
							 ->where('jenis_mitra_kerjasama.KODE', $keydata[0].'.'.$keydata[1])
							 ->get();
					$DBASE = $DBASE->toArray();
					$DBASE = json_decode(json_encode($DBASE), True);
			$return = DB::table($DBASE[0]['DBASE'].'_tingkat3')
							 ->select('NAMA','KODE_TINGKAT4 AS KODE')
							 ->where($DBASE[0]['DBASE'].'_tingkat3.KODE_TINGKAT3', $request->data)
							 ->get();
					$return = $return->toArray();
					$return = json_decode(json_encode($return), True);
		}
		

        return response()->success( [
            'message' => 'Sukses',
            'contents' => $return
        ]);
	}

	public function ListBranch($data, $token)
    {
		 $host = env('APP_URL');
	  if($host == 'http://api.dev.net/'){
		$urls = 'http://172.18.44.182/';
	}else{
		$urls = 'http://api.briconnect.bri.co.id/';  
	  }
      $client = new Client();
	 /*   $return =  Brispot::setEndpoint('region/v3')
				->setHeaders([
					'Authorization' => 'Bearer '.$token,
				])
                ->setBody([
                ])->get('form_params');

            return $return; */
 	  if($data['keys']=='kanwil'){
			  $requestListExisting = $client->request('GET', $urls.'bribranch/region/v3/'.$data['kode'],
				[
				  'headers' =>
				  [
					'Authorization' => 'Bearer '.$token
				  ]
				]
			  );
	  }elseif($data['keys']=='branch'){
				$requestListExisting = $client->request('GET', $urls.'bribranch/branch/'.$data['kode'],
				[
				  'headers' =>
				  [
					'Authorization' => 'Bearer '.$token
				  ]
				]
			  );
	  }elseif($data['keys']=='main'){
				$requestListExisting = $client->request('GET', $urls.'bribranch/mainbr/'.$data['kode'],
				[
				  'headers' =>
				  [
					'Authorization' => 'Bearer '.$token
				  ]
				]
			  );
	  }elseif($data['keys']=='all'){
				$requestListExisting = $client->request('GET', $urls.'bribranch/region/v3',
				[
				  'headers' =>
				  [
					'Authorization' => 'Bearer '.$token
				  ]
				]
			  );
	  } 
      $listExisting = json_decode($requestListExisting->getBody()->getContents(), true);

      return $listExisting;
    }
	

}
