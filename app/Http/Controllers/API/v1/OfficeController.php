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
        \Log::info($request->all());
        $branchs = $this->fetch($request);
        $page = $request->get('page', 1); // Get the ?page=1 from the url
        $perPage = $request->get('limit', 10); // Number of items per page
        $offset  = ($page * $perPage) - $perPage;
        $offices = [];

        if ($branchs['responseData'] != '') {
            $offices = collect($branchs['responseData'])->reject(function ($branch) {

                // Client mintanya kantor cabang aja, klo mau nambah tinggal tambah KCP atau KP
                return ! in_array($branch['jenis_uker'], ['KC']);
            })->slice($offset, $perPage)->values();
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
        \Log::info($request->all());
        return RestwsHc::setBody([
            'request' => json_encode([
                'requestMethod' => 'get_near_branch_v2',
                'requestData'   => [
                    'app_id' => 'mybriapi',
                    'kode_branch' => $request->get('branch', 0),
                    'distance'    => $request->get('distance', 10),

                    // if request latitude and longitude not present default latitude and longitude cimahi
                    'latitude'  => $request->get('lat', -6.884082),
                    'longitude' => $request->get('long', 107.541304),
                    'search' => $request->get('name', ''),
                ]
            ])
        ])
        ->post('form_params');
    }
}
