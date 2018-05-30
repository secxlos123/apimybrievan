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
	    $startDate = Carbon::parse($req->startDate)->startOfDay();
        $endDate = Carbon::parse($req->endDate)->endOfDay();
        $data = KartuKreditHistory::whereBetween('created_at', [$startDate, $endDate]);
        $datas = $data->get();
        $ajukanLength =  $data->where('kodeproses',1)->get();
        $verifikasiLength = $data->where('kodeproses','3.1')->get();
        $analisaLength = $data->where('kodeproses','6.1')->get();
        $approvedLength = $data->where('kodeproses','7.1')->get();
        $rejectedLength =  $data->where('kodeproses','8.1')->get();
	    return response()->json([
	    	'responseCode'=>'00',
	    	'responseMessage'=>'sukses',
	    	'totalLength'=>count($datas),
	    	'ajukanLength'=>count($ajukanLength),
	    	'verifikasiLength'=>count($verifikasiLength),
	    	'analisaLength' =>count($analisaLength),
	    	'approvedLength' => count($approvedLength),
	    	'rejectedLength' => count($rejectedLength),
	    	'contents'=>$datas
	    ]);
	}

	public function indexKanwil(Request $req){
		$startDate = Carbon::parse($req->str)->startOfDay();
        $endDate = Carbon::parse($req->end)->endOfDay();
        $region = $req->kanwil;
        $data = KartuKreditHistory::whereBetween('created_at', [$startDate, $endDate])->where('kanwil',$region);
        $datas = $data->get();
        $ajukanLength =  $data->where('kodeproses',1)->get();
        $verifikasiLength = $data->where('kodeproses','3.1')->get();
        $analisaLength = $data->where('kodeproses','6.1')->get();
        $approvedLength = $data->where('kodeproses','7.1')->get();
        $rejectedLength =  $data->where('kodeproses','8.1')->get();
	    return response()->json([
	    	'responseCode'=>'00',
	    	'responseMessage'=>'sukses',
	    	'totalLength'=>count($datas),
	    	'ajukanLength'=>count($ajukanLength),
	    	'verifikasiLength'=>count($verifikasiLength),
	    	'analisaLength' =>count($analisaLength),
	    	'approvedLength' => count($approvedLength),
	    	'rejectedLength' => count($rejectedLength),
	    	'contents'=>$datas
	    ]);
	}

	public function indexKanca(Request $req){
		$startDate = Carbon::parse($req->str)->startOfDay();
        $endDate = Carbon::parse($req->end)->endOfDay();
        $kanca = $req->branchId;
        $data = KartuKreditHistory::whereBetween('created_at', [$startDate, $endDate])->where('kanca',$kanca);
        $datas = $data->get();
        $ajukanLength =  $data->where('kodeproses',1)->get();
        $verifikasiLength = $data->where('kodeproses','3.1')->get();
        $analisaLength = $data->where('kodeproses','6.1')->get();
        $approvedLength = $data->where('kodeproses','7.1')->get();
        $rejectedLength =  $data->where('kodeproses','8.1')->get();
	    return response()->json([
	    	'responseCode'=>'00',
	    	'responseMessage'=>'sukses',
	    	'totalLength'=>count($datas),
	    	'ajukanLength'=>count($ajukanLength),
	    	'verifikasiLength'=>count($verifikasiLength),
	    	'analisaLength' =>count($analisaLength),
	    	'approvedLength' => count($approvedLength),
	    	'rejectedLength' => count($rejectedLength),
	    	'contents'=>$datas
	    ]);
	}
}