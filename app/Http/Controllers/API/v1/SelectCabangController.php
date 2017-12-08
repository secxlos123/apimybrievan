<?php

namespace App\Http\Controllers\API\v1;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Mitra;
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
	public function index( Request $request )
	{
		        \Log::info($request->all());
        $branchs = $this->fetch($request);
		 $page = $request->get('page', 1); // Get the ?page=1 from the url
        $perPage = $request->get('limit', 10000); // Number of items per page
        $offset  = ($page * $perPage) - $perPage;
        $offices = [];

		$mitra = Mitra::filter( $request )->get();
		$mitra = $mitra->toArray();
       
        if ($branchs['responseData'] != '') {
            foreach ($branchs['responseData'] as $branch) {
                $search = true;

                if ( $request->has('name') ) {
                    $search = strtoupper($request->input('name'));
                    $search = gettype( strpos($branch['unit_kerja'], $search) ) == 'integer';
                }

                if ( ( $search ) && ( $branch['jenis_uker'] == "KC" ) ) {
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
						foreach($mitra as $key){
							if($key['BRANCH_CODE']== $kode_uker){										
								$offices[] = $branch;
								\Log::info($mitra.'===='.$kode_uker.'/n');
							}
						}
                }
            }
		}
            $histories = new LengthAwarePaginator(
            $offices, // Only grab the items we need
            count($branchs['responseData']), // Total items
            $perPage, // Items per page
            $page, // Current page
            ['path' => $request->url(), 'query' => $request->query()] // We need this so we can keep all old query parameters from the url
        );

        $histories->transform(function ($history) {
            return [
                'branch' => $history['kode_uker'],
                'unit' => $history['unit_kerja'],
                'address' => $history['alamat'],
                'lat' => $history['latitude'],
                'long' => $history['longitude'],
            ];
        });

        return response()->success([
            'contents' => $histories,
            'message' => $branchs['responseDesc']
        ]);

	}
    private function fetch(Request $request)
    {
        \Log::info($request->all());
        $long = number_format($request->get('long', 106.813880), 5);
        $lat = number_format($request->get('lat', -6.217458), 5);
        $return = RestwsHc::setBody([
            'request' => json_encode([
                'requestMethod' => 'get_near_branch_v2',
                'requestData'   => [
                    'app_id' => 'mybriapi',
                    'kode_branch' => $request->get('BRANCH_CODE', 0),
                    'distance'    => $request->get('distance', 10),

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


}
