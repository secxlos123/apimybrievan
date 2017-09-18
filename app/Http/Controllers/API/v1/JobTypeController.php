<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Asmx;

class JobTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $job_type_list_service = Asmx::setEndpoint( 'GetJenisPekerjaan' )->setQuery( [
            'limit' => $request->limit,
            'page' => $request->page,
            'sort' => $request->sort,
        ] )->post();
        $job_type_list = $job_type_list_service[ 'contents' ];
        $job_type_list[ 'data' ] = array_map( function( $content ) {
            return [
                'id' => $content[ 'desc1' ],
                'name' => $content[ 'desc2' ]
            ];
        }, $job_type_list[ 'data' ] );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $job_type_list
        ], 200 );
    }
}
