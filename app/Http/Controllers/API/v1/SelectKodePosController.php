<?php

namespace App\Http\Controllers\API\v1;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\KodePos;

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
		$kodepost = '';
		$count = 0;
		foreach($kodepos as $key){
			if($kodedrkode!=$key['postal_code']){
				$kodepost[]['postal_code'] = $key['postal_code'];
				$kodepost[]['Kelurahan'] = $key['Kelurahan'];
				$kodepost[]['Kecamatan'] = $key['Kecamatan'];
				$kodepost[]['Kota'] = $key['Kota'];
				$kodepost[]['Propinsi'] = $key['Propinsi'];
				$kodedrkode = $key['postal_code'];
				$count = $count+1;
			}
		}
		
		if($kodepost==''){
			$kodepost = [];
		}

		return response()->success( [
            'message' => 'Sukses',
            'contents' => ['data'=>$kodepost,'count'=>$count]
        ], 200 );

	}


}
