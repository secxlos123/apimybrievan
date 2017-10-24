<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Asmx;

class JobFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $job_field_list_service = Asmx::setEndpoint( 'GetBidangPekerjaan' )->setQuery( [
            'search' => $request->search,
            'limit' => $request->limit,
            'page' => $request->page,
            'sort' => $request->sort,
        ] )->post();
        $job_field_list = $job_field_list_service[ 'contents' ];
        $job_field_list[ 'data' ] = array_map( function( $content ) {
            return [
                'id' => $content[ 'desc1' ],
                'name' => $content[ 'desc2' ]
            ];
        }, $job_field_list[ 'data' ] );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $job_field_list
        ], 200 );
    }
}
