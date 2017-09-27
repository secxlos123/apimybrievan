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
        $expiresAt = Carbon::now()->addMinutes(5);

        Cache::flush();

        if (! Cache::has('branchs') 
            || cache('lat') != $request->input('lat')
            || cache('long') != $request->input('long')
            || cache('branch') != $request->input('branch') 
            || cache('distance') != $request->input('distance') ) {
            Cache::put('branchs', $this->fetch($request), $expiresAt);
        }

        $branchs = Cache::get('branchs', function () use ($request) {
            return $this->fetch($request);
        });

        cache([ 'lat' => $request->input('lat') ], $expiresAt);
        cache([ 'long' => $request->input('long') ], $expiresAt);
        cache([ 'branch' => $request->input('branch') ], $expiresAt);
        cache([ 'distance' => $request->input('distance') ], $expiresAt);

        $page = $request->get('page', 1); // Get the ?page=1 from the url
        $perPage = $request->get('limit', 10); // Number of items per page
        $offset  = ($page * $perPage) - $perPage;
        $offices = [];

        if ($branchs['responseData'] != '') {
            $offices = collect($branchs['responseData'])->reject(function ($branch) {
                return ! in_array($branch['jenis_uker'], ['KCP', 'KC']);
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
        return RestwsHc::setBody([
            'request' => json_encode([
                'requestMethod' => 'get_near_branch_v2',
                'requestData'   => [
                    'app_id' => 'appidmybri',
                    'kode_branch' => $request->get('branch', 0),
                    'distance'    => $request->get('distance', 10),

                    // if request latitude and longitude not present default latitude and longitude cimahi
                    'latitude'  => $request->get('lat', -6.884082),
                    'longitude' => $request->get('long', 107.541304),
                ]
            ])
        ])
        ->post('form_params');
    }
}
