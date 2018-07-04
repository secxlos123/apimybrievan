<?php

namespace App\Models\Mitra;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Asmx;

class Mitra2 extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'mitra_detail_data';

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
					'deskripsi_mitra','hp_mitra','bendaharawan_mitra','telp_bendaharawan_mitra','hp_bendaharawan_mitra',
					'email','jml_pegawai','thn_pegawai','tgl_pendirian','akta_pendirian','akta_perubahan','npwp_usaha','id_header','laporan_keuangan','legalitas_perusahaan' ];
	
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
