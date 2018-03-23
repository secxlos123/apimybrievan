<?php

namespace App\Models\Mitra;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Asmx;

class registrasi_perjanjian extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'mitra_perjanjian';

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
					'perjanjian_layanan','jenis_perjanjian','judul_perjanjian','deskripsi_perjanjian','signer_mitra','nomor_notaril',
					'nomor_perjanjian_bri','nomor_perjanjian_ketiga','tgl_perjanjian','tgl_berakhir_perjanjian','tgl_perjanjian_backdate',
					'tgl_register','penilaian_mitra_register_radio','penilaian_mitra_kelayakan_radio','penilaian_mitra_rks_radio','pemutus_name_perjanjian',
					'pemeriksa_perjanjian','jabatan_perjanjian','jabatan_pemeriksa_perjanjian','upload_perjanjian','id_header'];
	
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
