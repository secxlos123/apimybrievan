<?php

namespace App\Http\Controllers\API\v1\Int;

use App\Http\Controllers\Controller;
use App\Models\KartuKreditHistory;
use Illuminate\Http\Request;
use App\Http\Requests\API\v1\KreditRequest;
use DB;
use Carbon\Carbon;

class KartuKreditDashboardController extends Controller{

	public function index(KreditRequest $req){
	    //select seluruh data cabang berdasarkan tanggal
	    $startDate = Carbon::parse($req->str)->startOfDay();
        $endDate = Carbon::parse($req->end)->endOfDay();
        $region = $req->kanwil;
        $data = KartuKreditHistory::whereBetween('created_at', [$startDate, $endDate])->where('kanwil',$region)->get();

        $ajukanLength =  $data->where('kodeproses','1')->count();
        $verifikasiLength = $data->where('kodeproses','3.1')->count();
        $analisaLength = $data->where('kodeproses','6.1')->count();
        $approvedLength = $data->where('kodeproses','7.1')->count();
        $rejectedLength =  $data->where('kodeproses','8.1')->count();
        return response()->json([
            'responseCode'=>'00',
            'responseMessage'=>'sukses',
            'totalLength'=>$data->count(),
            'ajukanLength'=>$ajukanLength,
            'verifikasiLength'=>$verifikasiLength,
            'analisaLength' =>$analisaLength,
            'approvedLength' => $approvedLength,
            'rejectedLength' => $rejectedLength,
            'contents'=>$data
        ]);
	}

	public function indexKanwil(Request $req){
		$startDate = Carbon::parse($req->str)->startOfDay();
        $endDate = Carbon::parse($req->end)->endOfDay();
        $region = $req->kanwil;
        $data = KartuKreditHistory::whereBetween('created_at', [$startDate, $endDate])->get();

        $ajukanLength =  $data->where('kodeproses','1')->count();
        $verifikasiLength = $data->where('kodeproses','3.1')->count();
        $analisaLength = $data->where('kodeproses','6.1')->count();
        $approvedLength = $data->where('kodeproses','7.1')->count();
        $rejectedLength =  $data->where('kodeproses','8.1')->count();
        return response()->json([
            'responseCode'=>'00',
            'responseMessage'=>'sukses',
            'totalLength'=>$data->count(),
            'ajukanLength'=>$ajukanLength,
            'verifikasiLength'=>$verifikasiLength,
            'analisaLength' =>$analisaLength,
            'approvedLength' => $approvedLength,
            'rejectedLength' => $rejectedLength,
            'contents'=>$data
        ]);
	}

	public function indexKanca(Request $req){
		$startDate = Carbon::parse($req->str)->startOfDay();
        $endDate = Carbon::parse($req->end)->endOfDay();
        $kanca = $req->branchId;
        $data = KartuKreditHistory::whereBetween('created_at', [$startDate, $endDate])->where('kanca',$kanca)->get();
        $ajukanLength =  $data->where('kodeproses',1)->count();
        $verifikasiLength = $data->where('kodeproses','3.1')->count();
        $analisaLength = $data->where('kodeproses','6.1')->count();
        $approvedLength = $data->where('kodeproses','7.1')->count();
        $rejectedLength =  $data->where('kodeproses','8.1')->count();
	    return response()->json([
	    	'responseCode'=>'00',
	    	'responseMessage'=>'sukses',
	    	'totalLength'=>$data->count(),
	    	'ajukanLength'=>$ajukanLength,
	    	'verifikasiLength'=>$verifikasiLength,
	    	'analisaLength' =>$analisaLength,
	    	'approvedLength' => $approvedLength,
	    	'rejectedLength' => $rejectedLength,
	    	'contents'=>$data
	    ]);
	}
}
