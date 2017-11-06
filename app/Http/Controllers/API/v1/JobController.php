<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Asmx;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $job_list_service = Asmx::setEndpoint( 'GetPekerjaan' )->setQuery( [
            'search' => $request->search,
            'limit' => $request->limit,
            'page' => $request->page,
            'sort' => $request->sort,
        ] )->post();
        $job_list = $job_list_service[ 'contents' ];
        $job_list[ 'data' ] = array_map( function( $content ) {
            return [
                'id' => $content[ 'desc1' ],
                'name' => $content[ 'desc2' ]
            ];
        }, $job_list[ 'data' ] );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $job_list
        ], 200 );
    }
}
