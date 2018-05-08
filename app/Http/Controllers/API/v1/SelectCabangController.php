<?php

namespace App\Http\Controllers\API\v1;


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
	public function getCabang( Request $request )
	{
		if($request->internal=='776f60e189baaeef54e5fab8a95e3af'){
		        \Log::info($request->all());
        $branchs = $this->fetch($request);
		$page = $request->get('page', 1); // Get the ?page=1 from the url
        $perPage = $request->get('limit', 10000); // Number of items per page
        $offset  = ($page * $perPage) - $perPage;
		$nilaisampai = 0;
        $offices = [];
		        if ($branchs['responseData'] != '') {
            foreach ($branchs['responseData'] as $branch) {
                $search = true;

                if ( $request->has('name') ) {
                    $search = strtoupper($request->input('name'));
                    $search = gettype( strpos($branch['unit_kerja'], $search) ) == 'integer';
                }

               // if ( ( $search ) && (( $branch['jenis_uker'] == "KC" ) || ( $branch['jenis_uker'] == "KCP" ) || ( $branch['jenis_uker'] == "BRI UNIT" ) || ( $branch['jenis_uker'] == "KCK" ) )) {
					$countkey = strlen($branch['kode_uker']);
					$kode_uker = '';
					if($countkey=='1'){
						$kode_uker = '0000'.$branch['kode_uker'];
					}elseif($countkey=='2'){
						$kode_uker = '000'.$branch['kode_uker'];
					}elseif($countkey=='3'){
						$kode_uker = '00'.$branch['kode_uker'];
					}elseif($countkey=='1'){
						$kode_uker = '0'.$branch['kode_uker'];
					}else{
						$kode_uker = $branch['kode_uker'];
					}
						$nilaicount =0;
						$request['key'] = $kode_uker;
						$mitra = Mitra::filter( $request )->get();
						$mitra = $mitra->toArray();
						$countmitra = count($mitra);
						for($i=0;$i<$countmitra;$i++){
//						$mitra[$i]['kanwil'] = $branch['kanwil'];
						$mitra[$i]['unit_induk'] = $branch['unit_induk'];
						$mitra[$i]['kanca_induk'] = $branch['kanca_induk'];
//						$mitra[$i]['jenis_uker'] = $branch['jenis_uker'];
//						$mitra[$i]['dati2'] = $branch['dati2'];
//						$mitra[$i]['dati1'] = $branch['dati1'];
						$mitra[$i]['alamat'] = $branch['alamat'];
//						$mitra[$i]['no_telp'] = $branch['no_telp'];
//						$mitra[$i]['no_fax'] = $branch['no_fax'];
//						$mitra[$i]['koordinat'] = $branch['koordinat'];
//						$mitra[$i]['latitude'] = $branch['latitude'];
//						$mitra[$i]['longitude'] = $branch['longitude'];
						$offices[] = $mitra[$i];

						}
             //   }
            }
		}

			$offices = $this->aasort($offices,"NAMA_INSTANSI");
			$countoffices = count($offices);
			$i = 0;
			$offics = array();
			foreach($offices as $offic => $x_office){
				$offics[$i] = $x_office;
				$i = $i+1;
			}
			
			$histories = new LengthAwarePaginator(
            $offics, // Only grab the items we need
            count($branchs['responseData']), // Total items
            $perPage, // Items per page
            $page, // Current page
            ['path' => $request->url(), 'query' => $request->query()] // We need this so we can keep all old query parameters from the url
        );
        $histories->transform(function ($history) {

            return $history;
        });

        return response()->success([
            'contents' => $histories,
            'message' => $branchs['responseDesc']
        ]);
		}else{
			$response = ['code'=>400,'descriptions'=>'Gagal','contents'=>''];
			 return $response;
		}
	}

		public function getCabangMitra( Request $request )
	{
		if($request->internal=='776f60e189baaeef54e5fab8a95e3af'){
	        \Log::info($request->all());
				
			$mitra = Mitra3::filter( $request )->get();
			$mitra = $mitra->toArray();
        return response()->success([
            'contents' => $mitra,
            'message' => 'Sukses'
        ]);
		}
		else{
			$response = ['code'=>400,'descriptions'=>'Gagal','contents'=>''];
			 return $response;
		}
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
  
	public function index( Request $request )
	{
		        \Log::info($request->all());
        $branchs = $this->fetch($request);
		$page = $request->get('page', 1); // Get the ?page=1 from the url
        $perPage = $request->get('limit', 10000); // Number of items per page
        $offset  = ($page * $perPage) - $perPage;
		$nilaisampai = 0;
        $offices = [];
		$countbranchs = count($branchs['responseData']);
		$x = 0;
		$kode_ukers = '';
		if ($countbranchs != 0) {
			foreach($branchs['responseData'] as $branch){
				$countkey = strlen($branch['kode_uker']);
//					if($branch['kode_uker']==$request->get('BRANCH_CODE')){
						if($x==0){
							if($countbranchs==1){
								$kode_ukers .= "('".$branch['kode_uker']."')";
							}else{
								$kode_ukers .= "('".$branch['kode_uker']."',";
								$x += 1;
							}
						}elseif($x==$countbranchs-1){
							$kode_ukers .= "'".$branch['kode_uker']."')";
								$x += 1;
						}else{
							$kode_ukers .= "'".$branch['kode_uker']."',";
							$x += 1;
						}
//					}
			}
		}
					$request['key'] = $kode_ukers;			
            		if($kode_ukers!=''){
						$nilaicount =0;
						$mitra = Mitra::filter( $request )->get();
						$mitra = $mitra->toArray();
						$countmitra = count($mitra);

						for($i=0;$i<$countmitra;$i++){
						$mitra[$i]['unit_induk'] = $branch['unit_induk'];
						$mitra[$i]['kanca_induk'] = $branch['kanca_induk'];
						$mitra[$i]['alamat'] = $branch['alamat'];
						$offices[] = $mitra[$i];
						}
					}

			$offices = $this->aasort($offices,"NAMA_INSTANSI");
			$countoffices = count($offices);
			$i = 0;
			$offics = array();
			foreach($offices as $offic => $x_office){
				$offics[$i] = $x_office;
				$i = $i+1;
			}
            $histories = new LengthAwarePaginator(
				$offics, // Only grab the items we need
				count($branchs['responseData']), // Total items
				$perPage, // Items per page
				$page, // Current page
				['path' => $request->url(), 'query' => $request->query()] // We need this so we can keep all old query parameters from the url
			);
			$histories->transform(function ($history) {
				return $history;
			});

        return response()->success([
            'contents' => $histories,
            'message' => $branchs['responseDesc']
        ]);
	}
    private function fetch(Request $request)
    {
        \Log::info($request->all());
	$branch_code = $request->get('BRANCH_CODE',0);
	if(strlen($branch_code)<4){
		$branch_code = '0';
	}else{
		$branch_code = $branch_code;
	}
        $long = number_format($request->get('long', env('DEF_LONG', '106.86758')), 5);
        $lat = number_format($request->get('lat', env('DEF_LAT', '-6.232423')), 5);
        $return = RestwsHc::setBody([
            'request' => json_encode([
                'requestMethod' => 'get_near_branch_v2',
                'requestData'   => [
                    'app_id' => 'mybriapi',
                    'kode_branch' => $branch_code,
                    'distance'    => $request->get('distance', 30),

                    // if request latitude and longitude not present default latitude and longitude cimahi
                    'latitude'  => $lat,
                    'longitude' => $long
                ]
            ])
        ])
        ->post('form_params');
        \Log::info($return);
        return $return;
    }
	public function getCabangMitraOpi( Request $request )
	{
		if($request->internal=='776f60e189baaeef54e5fab8a95e3af'){
	        \Log::info($request->all());
				
			$limit = $request->input( 'limit' ) ?: 10;
			$mitra = Mitra3::filter( $request )->paginate($limit);
			//$mitra = $mitra->toArray();
        return response()->success([
            'contents' => $mitra,
            'message' => 'Sukses'
        ]);
		}
		else{
			$response = ['code'=>400,'descriptions'=>'Gagal','contents'=>''];
			 return $response;
		}
	}

}
