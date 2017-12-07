<?php

namespace App\Http\Controllers\API\v1;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Mitra2;
use Sentinel;
use DB;

class SelectMitraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function SelectMitra( Request $request )
	{
        \Log::info($request->all());
        $mitra = Mitra2::filter( $request )->get();
		return response()->success( [
            'message' => 'Sukses',
            'contents' => [
                'data' => $mitra
            ]
        ], 200 );

	}


}
