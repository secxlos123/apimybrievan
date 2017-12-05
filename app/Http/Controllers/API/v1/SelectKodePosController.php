<?php

namespace App\Http\Controllers\API\v1;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\KodePos;
use Sentinel;
use DB;

class SelectKodePosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function SelectKodePos( Request $request )
	{
        \Log::info($request->all());
        $kodepos = KodePos::filter( $request )->get();
		$kodedrkode = '';		
	if(isset($kodepos['postal_code'])){
		foreach($kodepos as $key){
			if($kodedrkode!=$key['postal_code']){
			$kodepost[]['postal_code'] = $key['postal_code'];
			$kodedrkode = $key['postal_code'];
			}
		}
	}else{
		$kodepost = 'Data tidak ditemukan';
	}
        return response()->success( [
            'message' => 'Sukses',
            'contents' => ['data'=>$kodepost]
        ], 200 );

	}


}
