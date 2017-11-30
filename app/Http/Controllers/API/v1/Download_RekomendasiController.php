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
		/* $user = ['user_id' => '123',
			'username' =>'evan.tohape@lawencon.com',
            'email' => 'evan.tohap@lawencon.com',
            'first_name' => '123',
            'last_name'  => '123',
            'fullname'   => '123',
            'mobile_phone' => '123',   
            'role' => '1',
            'permission' => '1',
			'password'=>'w1nn3dini'];
			
        $baseData = $user;
		$user =	Sentinel::register( $baseData );
		 event( new CustomerRegister( $user, '123' ) ); */
	}
   
}
