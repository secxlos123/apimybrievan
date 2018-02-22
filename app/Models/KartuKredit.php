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

	//gambar gimana dong
    protected $fillable = [
    	'nik','hp','email',
    	'jenis_kelamin','nama','tempat_lahir','telephone',
    	'pendidikan','pekerjaan','tiering_gaji',
    	'agama','jenis_nasabah','pilihan_kartu',
    	'penghasilan_perbulan','jumlah_penerbit_kartu',
    	'memiliki_kk_bank_lain','limit_tertinggi'
    ];

    protected $hidden = [
        'id'
    ];
}
