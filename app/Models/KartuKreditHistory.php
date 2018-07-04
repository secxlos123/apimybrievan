<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RestwsHc;

class KartuKreditHistory extends Model
{
    protected $table = 'kartu_kredit_histories';

    protected $fillable = ['apregno','kodeproses','kanwil','pn','kanca','ao_name', 'nama_pinca', 'pn_pinca'];

    protected $appends = ['deskripsi_proses'];

    protected $hidden = ['id','updated_at'];

   

    public function getDeskripsiProsesAttribute($val){
        $val = $this->kodeproses;
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

    public function createHistory($pn,$apregno,$branchId,$ao_name){
        $kanwil = $this->getKanwilByBranchId($branchId);
        $data['apregno'] = $apregno;
        $data['kodeproses'] = '1';
        $data['kanwil'] = $kanwil;
        $data['kanca'] = $branchId;
        $data['pn'] = $pn;
        $data['ao_name'] = $ao_name;

        $createHistory = $this->create($data);
        return $createHistory;

    }

    public function updateKodeProses($kode,$apregno){
        $update = $this->where('apregno',$apregno)->update([
            'kodeproses'=>$kode
        ]);

    }

    public function updatePinca($nama_pinca, $pn_pinca, $apregno)
    {
        $updatePinca = $this->where('apregno', $apregno)->update([
            'nama_pinca' => $nama_pinca,
            'pn_pinca' => $pn_pinca
        ]);
    }

    //ambil kanwil / region dari result list uker kanca.
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

        $res = $list_uker_kanca['responseData'];
        $res = $res[0];

        $kanwil = $res['region'];
        return $kanwil;
    }
}
