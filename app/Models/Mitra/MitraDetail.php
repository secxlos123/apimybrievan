<?php

namespace App\Models\Mitra;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Asmx;

class MitraDetail extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'mitra_detail';

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
						'alamat_mitra','no_telp_mitra','deskripsi_mitra','hp_mitra','bendaharawan_mitra',
						'telp_bendaharawan_mitra','hp_bendaharawan_mitra','jml_pegawai','thn_pegawai',
						'tgl_pendirian','akta_pendirian','akta_perubahan','npwp_usaha','laporan_keuangan','legalitas_perusahaan',
						'no_rek_mitra','tipe_account','tgl_pembayaran','tgl_gajian','jenis_pengajuan','bank_jenis_pinjaaman',
						'fasilitas_bank','upload_fasilitas_bank','ijin_perinsip','upload_ijin','daftar_ijin','fasilitas_lainnya',
						'deskripsi_fasilitas_lainnya','nomor_pks_notaril','nomor_perjanjian_kerjasama_bri',
						'nomor_perjanjian_kerjasama_ketiga','tgl_perjanjian','tgl_perjanjian_backdate','ijin_prinsip','id_detail'];
	
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'id_gimmick' ];

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getIdAttribute( $value )
    {
        return $this->id_gimmick;
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

 
    public function scopeFilter( $query, Request $request )
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];
        $user = \RestwsHc::getUser();

        if ( $sort[0] == "id" ) {
            $sort = ['id', 'asc'];
        }

		$hari_ini = date("Y/m/d") ;
		
		 $dir = $query->where( function( $dir ) use( $request,$hari_ini ) {
            if ( $request->has('gimmick_name') ) {
                $dir = $dir->where('gimmick.gimmick_name', $request->input('gimmick_name'));
			} 
			
		\Log::info($hari_ini);
			
//			if ( $request->has('tgl_mulai')&&$request->has('tgl_berakhir') ) {
                $dir = $dir->where('gimmick.tgl_mulai','<', $hari_ini);
                $dir = $dir->where('gimmick.tgl_berakhir','>', $hari_ini);
//			}
        } );
		 $dir = $dir->join('gimmick_detail', 'gimmick_detail.id_header', '=', 'gimmick.id_header');
        $dir = $dir->orderBy('gimmick.'.$sort[0], $sort[1]);

        \Log::info($dir->toSql());
        \Log::info($dir->getBindings());

        return $dir;
    }

}
