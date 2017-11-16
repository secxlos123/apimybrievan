<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Asmx;

class UseReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $economy_sector_list_service = Asmx::setEndpoint( 'GetTujuanPenggunaan' )->setQuery( [
            'search' => $request->search,
            'limit' => $request->limit,
            'page' => $request->page,
            'sort' => $request->sort,
        ] )->post();
        $economy_sector_list = $economy_sector_list_service[ 'contents' ];
        $economy_sector_list[ 'data' ] = array_map( function( $content ) {
            return [
                'id' => $content[ 'id_tujuan' ],
                'name' => $content[ 'desc1' ]
            ];
        }, $economy_sector_list[ 'data' ] );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $economy_sector_list
        ], 200 );
    }
}
