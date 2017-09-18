<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Asmx;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        $project_list_service = Asmx::setEndpoint( 'GetProject' )->setQuery( [
            'limit' => $request->limit,
            'page' => $request->page,
            'sort' => $request->sort,
        ] )->post();
        $project_list = $project_list_service[ 'contents' ];
        $project_list[ 'data' ] = array_map( function( $content ) {
            return [
                'id' => $content[ 'id_project' ],
                'name' => $content[ 'nama' ]
            ];
        }, $project_list[ 'data' ] );
        return response()->success( [
            'message' => 'Sukses',
            'contents' => $project_list
        ], 200 );
    }
}
