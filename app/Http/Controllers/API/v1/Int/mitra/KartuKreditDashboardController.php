<?php

namespace App\Http\Controllers\API\v1\Int;

use App\Models\KartuKreditHistory;
use App\Http\Requests\API\v1\KreditRequest;
use DB;

class KartuKreditDashboardController extends Controller{

	public function index(KreditRequest $req){
	    //select seluruh data cabang berdasarkan tanggal
	    $startDate = $req->startDate;
	    $endDate = $req->endDate;
	    $data = KartuKreditHistory::whereNotBetween('created_at', [$startDate, $endDate])->get();
	    return response()->json([
	    	'responseCode'=>'00',
	    	'responseMessage'=>'sukses',
	    	'length'=>count($data),
	    	'contents'=>$data
	    ]);
	}
}