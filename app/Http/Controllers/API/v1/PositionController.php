<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Asmx;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $position_list_service = Asmx::setEndpoint( 'GetJabatan' )->setQuery( [
            'search' => $request->search,
            'limit'  => $request->limit,
            'page'   => $request->page,
            'sort'   => $request->sort
        ] )->post();
        $positions = $position_list_service[ 'contents' ];
        $positions[ 'data' ] = array_map( function( $content ) {
            return [
                'id' => $content[ 'desc1' ],
                'name' => $content[ 'desc2' ]
            ];
        }, $positions[ 'data' ] );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $positions
        ], 200 );
    }
}
