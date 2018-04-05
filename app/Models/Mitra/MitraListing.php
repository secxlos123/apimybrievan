<?php namespace App\Models\Mitra;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;
use Sentinel;
use Asmx;
use RestwsHc;
use DB;
use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;

class MitraListing extends Authenticatable  {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'mitra_utama';
	
	   public function scopeFilter( $query, Request $request )
    {
		        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id_header', 'asc'];
        $user = \RestwsHc::getUser();
        if ( $sort[0] == "id_header" ) {
            $sort = ['id_header', 'asc'];
        }
		 $dir = $query->where( function( $dir ) use( $request ) {
            if ( $request->has('NAMA_INSTANSI') ) {
                $dir = $dir->where('mitra_utama.NAMA_INSTANSI', $request->input('NAMA_INSTANSI'));
			}
            if ( $request->has('UNIT_KERJA') ) {
                $dir = $dir->where('mitra_utama.UNIT_KERJA', $request->input('UNIT_KERJA'));
			}
        } );
				$dir = $dir->select([
                    \DB::Raw('mitra_utama."idMitrakerja",mitra_utama."NAMA_INSTANSI",mitra_utama."UNIT_KERJA",mitra_detail_dasar.status,mitra_detail_fasilitas_perbankan.nomor_perjanjian_kerjasama_bri,mitra_detail_dasar.golongan_mitra')
					 ]);
				 $dir = $dir->groupBy([\DB::Raw('mitra_utama."NAMA_INSTANSI",mitra_utama."UNIT_KERJA",mitra_detail_dasar."status",
						mitra_detail_fasilitas_perbankan.nomor_perjanjian_kerjasama_bri,mitra_detail_dasar.golongan_mitra,mitra_utama."idMitrakerja"')]);
				 $dir = $dir->join('mitra_detail_fasilitas', 'mitra_utama.idMitrakerja', '=', 'mitra_detail_fasilitas.id_header');
				 $dir = $dir->join('mitra_detail_fasilitas_perbankan', DB::raw('cast("mitra_detail_fasilitas".fasilitas_bank as int)'), '=', 'mitra_detail_fasilitas_perbankan.id');
				 $dir = $dir->join('mitra_detail_dasar', 'mitra_utama.idMitrakerja', '=', 'mitra_detail_dasar.id_header');

        return $dir;
    }


}
