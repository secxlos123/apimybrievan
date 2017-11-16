<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Asmx;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $program_list_service = Asmx::setEndpoint( 'GetProgram' )->setQuery( [
            'search' => $request->search,
            'limit' => $request->limit,
            'page' => $request->page,
            'sort' => $request->sort,
        ] )->post();
        $program_list = $program_list_service[ 'contents' ];
        $program_list[ 'data' ] = array_map( function( $content ) {
            return [
                'id' => $content[ 'gimmick_id' ],
                'name' => $content[ 'program_name' ]
            ];
        }, $program_list[ 'data' ] );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $program_list
        ], 200 );
    }
}
