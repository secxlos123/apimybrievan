<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;
use Sentinel;
use Asmx;
use RestwsHc;
use DB;
use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;

class Mitra4 extends Authenticatable  {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'mitra';
	
	   public function scopeFilter( $query, Request $request )
    {
		
	  $kode= '';
        $mitra = $query->where( function( $mitra ) use( $request, &$user ) {
						$long = '106.86758';
						$lat = '-6.232423';
						$distance = '30';
						if($request->has('long')){
							$long = $request->input('long');
						}
						if($request->has('lat')){
							$lat = $request->input('lat');
						}
						if($request->has('distance')){
							$distance = $request->input('distance');
						}
						$key = $request->input('key');
						 if( $request->has( 'key' ) ) {
								$BRANCH_CODE = $request->input('key');
								$branchcis ='';
								if(strlen($BRANCH_CODE)=='5'){
									$branchcis = $BRANCH_CODE;
									/* for($i=0;$i<5;$i++){
										$cek = substr($BRANCH_CODE,$i,1);
										if($cek!=0){
											$branchcis = substr($BRANCH_CODE,$i,4);
											$i = 5;
										}
									} */
								}else{								
										$o = strlen($BRANCH_CODE);
										$branchut = '';
										for($y=$o;$y<5;$y++){
											if($y==$o){
												$branchut = '0'.$BRANCH_CODE;
											}else{
												$branchut = '0'.$branchut;
											}
										} 
										$branchcis = $branchut;	
								}
								\Log::info($branchcis);
						 }
						 $mitra->whereRaw('mitra."BRANCH_CODE" IN (select distance_uker('."'".$lat."','".$long."','".$distance."'))");
						 //$mitra->Where('BRANCH_CODE', $key);
        } );
			 if(!$request->has( 'internal' )){
				
				$kode = $request->input('kode');
				$mitra->whereRaw('LOWER("NAMA_INSTANSI") LIKE ? ',["%".trim(strtolower($kode))."%"]);
			} 
				//$mitra->where('LOWER(NAMA_INSTANSI)','like','%LOWER('.$kode.')%');
				$mitra->orderBy('NAMA_INSTANSI', 'ASC');
				$mitra = $mitra->select([
                    \DB::Raw('mitra."idMitrakerja",mitra."NAMA_INSTANSI",mitra."NPL",mitra.kode,mitra."BRANCH_CODE",mitra."Jumlah_pegawai",mitra."JENIS_INSTANSI",mitra."Scoring",
					mitra."KET_Scoring",mitra.jenis_bidang_usaha,mitra.alamat_instansi,mitra.alamat_instansi3,mitra.
					telephone_instansi,mitra.rating_instansi,mitra.lembaga_pemeringkat,mitra.tanggal_pemeringkat,mitra.go_public,mitra.no_ijin_prinsip,mitra.
					date_updated,mitra.updated_by,mitra.acc_type,mitra.alamat_instansi2,b."UNIT_KERJA"'),
                     \DB::Raw(" case when mitra.kode is not null then 2 else 1 end as new_order ")
					 ]);
				$mitra->leftJoin(
				 DB::raw('(SELECT kode_uker, max(unit_kerja) "UNIT_KERJA" from uker_tables GROUP BY kode_uker) b'),
				 'mitra.BRANCH_CODE','=',DB::raw("CASE
WHEN LENGTH(kode_uker)=5 THEN b.kode_uker
WHEN LENGTH(kode_uker)=4 THEN '0'||b.kode_uker
WHEN LENGTH(kode_uker)=3 THEN '00'||b.kode_uker
WHEN LENGTH(kode_uker)=2 THEN '000'||b.kode_uker
WHEN LENGTH(kode_uker)=1 THEN '0000'||b.kode_uker
END"));						
        \Log::info($mitra->toSql());

        return $mitra;
    }


}
