<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KartuKreditController extends Controller{
	
	public function example(){

		return response()->json([
                'name' => 'Abigail',
                'state' => 'CA'
            ]);

		// return "abc";
	}
}

 