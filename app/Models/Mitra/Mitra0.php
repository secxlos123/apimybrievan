<?php

namespace App\Models\Mitra;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Asmx;

class Mitra0 extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'mitra_utama';

    /**
     * Disabling timestamp feature.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	 

					
    protected $fillable = [  	
					'idMitrakerja','NAMA_INSTANSI','kode','NPL','BRANCH_CODE','Jumlah_pegawai','JENIS_INSTANSI','UNIT_KERJA',
					'Scoring','KET_Scoring','jenis_bidang_usaha','alamat_instansi','alamat_instansi2','alamat_instansi3','telephone_instansi',
					'rating_instansi','lembaga_pemeringkat','go_public','no_ijin_prinsip','date_updated','updated_by','acc_type'];
	
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'id' ];

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getIdAttribute( $value )
    {
        return $this->id;
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public static function create( $data ) {
	  try {        

        $gimmick = ( new static )->newQuery()->create($data);
            return $gimmick;
     } catch (Exception $e) {
            return $e;    
    }

       
    }

}
