<?php

namespace App\Http\Controllers\API\v1;

use App\Order;
use App\Mail\suratrekomendasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class Download_RekomendasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function Download(Request $request)
	{ 
	// file location
		

// return mail with an attachment
		$content =	Mail::to($request)->send(new suratrekomendasi());
	 return response()->success( [
            'message' => 'Sukses',
            'contents' => $content
        ], 200 );
		
	}
   
}
