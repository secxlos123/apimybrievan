<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KartuKredit extends Model
{
	//=================INFO=================
	//jenis nasabah -> diisi "nasabah" atau "debitur" 
	//1. nasabah = nasabah yang tidak memiliki pinjaman
	//2. debitur = nasabah yang memili pinjamna
	//======================================
	//pilihan kartu -> kartu yang user ajukan. diisi "world access","easy card", "platinum","touch"

    protected $fillable = [
    	'nik','hp','email','nama_ibu_kandung','status','ttl','jenis_kelamin',
    	'nama','tempat_lahir','telephone','pendidikan','cif','pekerjaan',
    	'tiering_gaji','npwp','agama','jenis_nasabah','pilihan_kartu',
    	'penghasilan_diatas_10_juta','jumlah_penerbit_kartu',
    	'memiliki_kk_bank_lain'
    ]
}
