<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Asmx;

class CompanyInsurance extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $insurance_service = Asmx::setEndpoint( 'GetPerusahaanAsuransi' )->setQuery( [
            'search' => $request->search,
            'limit' => $request->limit,
            'page' => $request->page,
            'sort' => $request->sort,
        ] )->setBody(['request'=>''])->post('form_params');
        \Log::info($insurance_service);
        $insurance = $insurance_service[ 'contents' ];
        $insurance[ 'data' ] = array_map( function( $content ) {
            return [
                'id' => $content[ 'id_perusahaan_asuransi' ],
                'name' => $content[ 'desc2' ],
                'code' => $content[ 'desc3' ]
            ];
        }, $insurance[ 'data' ] );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $insurance
        ], 200 );
    }
}
