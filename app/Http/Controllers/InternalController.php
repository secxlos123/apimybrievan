<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InternalController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Show error response when request to BRI result error.
     *
     * @param  array $data
     * @return \Illuminate\Http\Response
     */
    public function showBRIResponseMessage( $data )
    {
        return response()->success( [
            'message' => $data[ 'responseData' ],
            'contents'=> []
        ], 400 );
    }
}
