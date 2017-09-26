<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Asmx;

class CitizenshipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $citizenship_list_service = Asmx::setEndpoint( 'GetNegara' )->setQuery( [
            'search' => $request->search,
            'limit' => $request->limit,
            'page' => $request->page,
            'sort' => $request->sort,
        ] )->post();
        $citizenship_list = $citizenship_list_service[ 'contents' ];
        $citizenship_list[ 'data' ] = array_map( function( $content ) {
            return [
                'id' => $content[ 'desc1' ],
                'name' => $content[ 'desc2' ]
            ];
        }, $citizenship_list[ 'data' ] );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $citizenship_list
        ], 200 );
    }
}
