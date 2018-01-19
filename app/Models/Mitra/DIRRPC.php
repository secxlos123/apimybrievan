<?php

namespace App\Models\Mitra;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Asmx;

class MitraList extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'mitra_header';

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
		'registrasi_mitra_header','jenis_mitra','golongan_mitra','induk_mitra','anak_perusahaan_wilayah',
		'anak_perusahaan_kabupaten','id_mitra','email','payroll','bank_payroll','status','id_detail','id_approval_pemutus','id_header',
						'alamat_mitra','no_telp_mitra','deskripsi_mitra','hp_mitra','bendaharawan_mitra',
						'telp_bendaharawan_mitra','hp_bendaharawan_mitra','jml_pegawai','thn_pegawai',
						'tgl_pendirian','akta_pendirian','akta_perubahan','npwp_usaha','laporan_keuangan','legalitas_perusahaan',
						'no_rek_mitra','tipe_account','tgl_pembayaran','tgl_gajian','jenis_pengajuan','bank_jenis_pinjaaman',
						'fasilitas_bank','upload_fasilitas_bank','ijin_perinsip','upload_ijin','daftar_ijin','fasilitas_lainnya',
						'deskripsi_fasilitas_lainnya','nomor_pks_notaril','nomor_perjanjian_kerjasama_bri',
						'nomor_perjanjian_kerjasama_ketiga','tgl_perjanjian','tgl_perjanjian_backdate','ijin_prinsip','id_detail',		
						'pemutus','jabatan','pemeriksa','jabatan_pemeriksa','status','id_approval'];
	
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
    public function scopeFilter( $query, Request $request )
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id_header', 'asc'];
        $user = \RestwsHc::getUser();

        if ( $sort[0] == "id_header" ) {
            $sort = ['id_header', 'asc'];
        }

		 $dir = $query->where( function( $dir ) use( $request ) {
            if ( $request->has('jenis_mitra') ) {
                $dir = $dir->where('mitra_header.jenis_mitra', $request->input('jenis_mitra'));
			}
            if ( $request->has('anak_perusahaan_wilayah') ) {
                $dir = $dir->where('mitra_header.anak_perusahaan_wilayah', $request->input('anak_perusahaan_wilayah'));
			}
            if ( $request->has('anak_perusahaan_kabupaten') ) {
                $dir = $dir->where('mitra_header.anak_perusahaan_kabupaten', $request->input('anak_perusahaan_kabupaten'));
			}
        } );
				 $dir = $dir->join('mitra_detail', 'mitra_header.id_detail', '=', 'mitra_detail.id_detail');
        $dir = $dir->orderBy('mitra_header.'.$sort[0], $sort[1]);

        \Log::info($dir->toSql());
        \Log::info($dir->getBindings());

        return $dir;
    }

  

}
