<?php

namespace App\Http\Controllers\API\v1\Int;

use App\Models\KartuKreditHistory;
use App\Http\Requests\API\v1\KreditRequest;
use DB;
use Carbon\Carbon;

class KartuKreditDashboardController extends Controller{

	public function index(KreditRequest $req){
	    //select seluruh data cabang berdasarkan tanggal
	    $startDate = Carbon::parse($req->str)->startOfDay();
        $endDate = Carbon::parse($req->end)->endOfDay();
        $data = KartuKreditHistory::whereBetween('created_at', [$startDate, $endDate])->get();
	    return response()->json([
	    	'responseCode'=>'00',
	    	'responseMessage'=>'sukses',
	    	'length'=>count($data),
	    	'contents'=>$data
	    ]);
	}

	public function indexKanwil(Request $req){
		$startDate = $req->startDate;
	    $endDate = $req->endDate;


	}
	public function indexKanca(Request $req){

	}
}