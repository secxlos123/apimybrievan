<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KartuKreditHistory extends Model
{
    protected $table = 'kartu_kredit_history';

    protected $fillable = ['apregno','kodeproses','kanwil','pn'];

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
}
