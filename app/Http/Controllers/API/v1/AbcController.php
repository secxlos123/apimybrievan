<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AbcController extends Controller{
	public function callMe(){
		return response()->json([
                'name' => 'Abigail',
                'state' => 'CA'
            ]);
	}
}

 