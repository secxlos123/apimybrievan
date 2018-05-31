<?php

namespace App\Http\Controllers\API\v1\Int;

use App\Http\Controllers\Controller;
use App\Models\KartuKreditHistory;
use Illuminate\Http\Request;
use App\Http\Requests\API\v1\KreditRequest;
use DB;
use Carbon\Carbon;
use RestwsHc;

class KartuKreditDashboardController extends Controller{

	public function index(KreditRequest $req){
	    //select seluruh data cabang berdasarkan tanggal
	    //response berdasarkan kanwil
	    $startDate = Carbon::parse($req->startDate)->startOfDay();
        $endDate = Carbon::parse($req->endDate)->endOfDay();

        $data = KartuKreditHistory::whereBetween('created_at', [$startDate, $endDate])->get();

        $contents = [];

        $listKanwil = $this->getListKanwil();

        foreach ($listKanwil as $val) {
        	$newData = $data->where('kanwil',$val['region_id']);
        	$ajukanLength =  $newData->where('kodeproses','1')->count();
	        $verifikasiLength = $newData->where('kodeproses','3.1')->count();
	        $analisaLength = $newData->where('kodeproses','6.1')->count();
	        $approvedLength = $newData->where('kodeproses','7.1')->count();
	        $rejectedLength =  $newData->where('kodeproses','8.1')->count();
        	$pushData = [
        		'region_id'=>$val['region_id'],
         		'region_name'=>$val['region_name'],
         		'branch_id'=>$val['branch_id'],
         		'totalLength' => $newData->count(),
          		'ajukanLength'=>$ajukanLength,
	            'verifikasiLength'=>$verifikasiLength,
	            'analisaLength' =>$analisaLength,
	            'approvedLength' => $approvedLength,
	            'rejectedLength' => $rejectedLength,
        	];
        	array_push($contents, $pushData);
        };

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
            'contents'=>$contents
        ]);
	}

	function getListKanwil(){
		$sendRequest['app_id'] = 'mybriapi';
    	$list_kanwil = RestwsHc::setBody([
            'request' => json_encode([
                'requestMethod' => 'get_list_kanwil',
                'requestData' => $sendRequest,
            ])
        ])
        ->post( 'form_params' );

        if ($list_kanwil['responseCode'] == '00' ) {
	         $list_kanwil = array_map( function( $content ) {
	          return [
	              'region_id' => $content[ 'region' ],
	              'region_name' => $content[ 'rgdesc' ],
	              'branch_id' => $content[ 'branch' ]
	          ];
	      }, $list_kanwil['responseData']);
	         return $list_kanwil;
    	 }else{
    	 	return response()->error([
    	 		'responseCode'=>'01',
    	 		'responseMessage'=>'terjadi kesalahan. gagal mendapatkan list kanwil'
    	 	],422);
    	 }
	}

	public function indexKanwil(Request $req){
		$startDate = Carbon::parse($req->startDate)->startOfDay();
        $endDate = Carbon::parse($req->endDate)->endOfDay();
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

	public function indexKanca(Request $req){
		$startDate = Carbon::parse($req->startDate)->startOfDay();
        $endDate = Carbon::parse($req->endDate)->endOfDay();
        $kanca =  '00'.$req->branchId;
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
