<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Asmx;

class IndependentAppraiser extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $appraiser_service = Asmx::setEndpoint( 'GetPenilaiIndependen' )->setQuery( [
            'search' => $request->search,
            'limit' => $request->limit,
            'page' => $request->page,
            'sort' => $request->sort,
        ] )->setBody(['request'=>''])->post('form_params');
        \Log::info($appraiser_service);
        $appraiser = $appraiser_service[ 'contents' ];
        $appraiser[ 'data' ] = array_map( function( $content ) {
            return [
                'id' => $content[ 'id_penilai_independen' ],
                'name' => $content[ 'desc' ],
            ];
        }, $appraiser[ 'data' ] );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $appraiser
        ], 200 );
    }
}
