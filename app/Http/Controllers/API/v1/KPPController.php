<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Asmx;

class KPPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $kpp_list_service = Asmx::setEndpoint( 'GetJenisKPP' )->setQuery( [
            'limit' => $request->limit,
            'page' => $request->page,
            'sort' => $request->sort,
        ] )->post();
        $kpp_list = $kpp_list_service[ 'contents' ];
        $kpp_list[ 'data' ] = array_map( function( $content ) {
            return [
                'id' => $content[ 'desc1' ],
                'name' => $content[ 'desc2' ]
            ];
        }, $kpp_list[ 'data' ] );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $kpp_list
        ], 200 );
    }
}
