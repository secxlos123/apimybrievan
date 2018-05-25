<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RestwsHc;

class KartuKreditHistory extends Model
{
    protected $table = 'kartu_kredit_history';

    protected $fillable = ['apregno','kodeproses','kanwil','pn','kanca'];

    protected $appends = ['deskripsi_proses'];

    public function getDescriptionProsesAttribute($val){
    	if($val == '1'){
    		return 'Ajukan';
    	}else if($val == '3.1'){
    		return 'Verifikasi';
    	}else if($val == '6.1'){
    		return 'Analis';
    	}else if($val == '7.1'){
    		return 'Putusan Approve';
    	}else if($val == '8.1'){
    		return 'Putusan Reject';
    	}
    }

    public function createHistory($pn,$apregno,$branchId){
        $kanwil = $this->getKanwilByBranchId($branchId);
        $data['apregno'] = $apregno;
        $data['kodeproses'] = '1';
        $data['kanwil'] = $kanwil;
        $data['kanca'] = $branchId;
        $data['pn'] = $pn;

        $createHistory = $this->create($data);
        return $createHistory;

    }

    public function updateKreditHistoryKodeProsesTo($kodeProses){

    }
    //ambil kanwil / region dari result list uker kanca.
    //CUMA BISA DI PROD
    public function getKanwilByBranchId($branch){
        $requestPost =[
            'app_id' => 'mybriapi',
            'branch_code' => $branch
        ];

        $list_uker_kanca = RestwsHc::setBody([
                    'request' => json_encode([
                            'requestMethod' => 'get_list_uker_from_cabang',
                            'requestData' => $requestPost
                    ])
            ])
            ->post( 'form_params' );

        $res = $list_uker_kanca->responseData[0];
        $kanwil = $res->region;
        return $kanwil;
    }
}
