<?php

namespace App\Http\Controllers\API\v1\Int;

use App\Http\Controllers\Controller;
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
		$startDate = Carbon::parse($req->str)->startOfDay();
        $endDate = Carbon::parse($req->end)->endOfDay();
        $region = $req->kanwil;
        $data = KartuKreditHistory::whereBetween('created_at', [$startDate, $endDate])->where('kanwil',$region)
        ->get();
        return response()->json([
	    	'responseCode'=>'00',
	    	'responseMessage'=>'sukses',
	    	'length'=>count($data),
	    	'contents'=>$data
	    ]);
	}

	public function indexKanca(Request $req){
		$startDate = Carbon::parse($req->str)->startOfDay();
        $endDate = Carbon::parse($req->end)->endOfDay();
        $kanca = $req->branchId;
        $data = KartuKreditHistory::whereBetween('created_at', [$startDate, $endDate])->where('kanca',$kanca)
        ->get();
        return response()->json([
	    	'responseCode'=>'00',
	    	'responseMessage'=>'sukses',
	    	'length'=>count($data),
	    	'contents'=>$data
	    ]);
	}
}