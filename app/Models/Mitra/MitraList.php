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
    public function scopeFilter( $query, Request $request )
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id_header', 'asc'];
        $user = \RestwsHc::getUser();

        if ( $sort[0] == "id_header" ) {
            $sort = ['id_header', 'asc'];
        }

		 $dir = $query->where( function( $dir ) use( $request ) {
            if ( $request->has('NAMA_INSTANSI') ) {
                $dir = $dir->where('mitra_header.NAMA_INSTANSI', $request->input('NAMA_INSTANSI'));
			}
            if ( $request->has('UNIT_KERJA') ) {
                $dir = $dir->where('mitra_header.UNIT_KERJA', $request->input('UNIT_KERJA'));
			}
        } );

				 $dir = $dir->join('mitra_detail', 'mitra_header.id_detail', '=', 'mitra_detail.id_detail');
        $dir = $dir->orderBy('mitra_header.'.$sort[0], $sort[1]);

        \Log::info($dir->toSql());
        \Log::info($dir->getBindings());

        return $dir;
    }

  

}
