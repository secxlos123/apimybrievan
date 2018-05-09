<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use RestwsHc;
use Cache;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $branchs = $this->fetch($request);
        $page = $request->get('page', 1); // Get the ?page=1 from the url
        $perPage = $request->get('limit', 10000); // Number of items per page
        $offset  = ($page * $perPage) - $perPage;
        $offices = [];
        \Log::info("=======Response Office===========");
        \Log::info($branchs['responseData']);
        if ($branchs['responseData'] != '') {
            foreach ($branchs['responseData'] as $branch) {
                $search = true;

                if ( $request->has('name') ) {
                    $search = strtoupper($request->input('name'));
                    $search = gettype( strpos($branch['unit_kerja'], $search) ) == 'integer';
                }

                if ( ( $search ) && ( $branch['jenis_uker'] == "KC" ) ) {
                    $offices[] = $branch;
                }
            }
        }

        /**
         * Generate pagination
         */
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

    /**
     * Fetch data from internal BRI
     *
     * @param  Request $request [description]
     * @return array
     */
    private function fetch(Request $request)
    {
        $long = number_format($request->get('long', env('DEF_LONG', '106.81350')), 5);
        $lat = number_format($request->get('lat', env('DEF_LAT', '-6.21670')), 5);
        $return = RestwsHc::setBody([
            'request' => json_encode([
                'requestMethod' => 'get_near_branch_v2',
                'requestData'   => [
                    'app_id' => 'mybriapi',
                    'kode_branch' => $request->get('branch', 0),
                    'distance'    => $request->get('distance', 10),

                    // if request latitude and longitude not present default latitude and longitude cimahi
                    'latitude'  => $lat,
                    'longitude' => $long
                ]
            ])
        ])
        ->post('form_params');
        return $return;
    }
}
