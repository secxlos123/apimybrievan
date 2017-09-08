<?php

namespace App\Http\Controllers\API\v1\Int;

use Illuminate\Http\Request;
use App\Http\Controllers\InternalController as Controller;

class TokenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        return response()->success( [
            'message' => 'Sukses',
            'contents' => [
                'refreshed' => false,
                'token' => $request->header( 'Authorization' )
            ]
        ], 200 );
    }
}
