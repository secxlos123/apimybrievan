<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;
use Sentinel;
use Asmx;
use RestwsHc;
use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;

class Mitra extends Authenticatable  {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'mitra';
	
	  public function scopeFilter( $query, Request $request )
    {
        $mitra = $query->where( function( $mitra ) use( $request, &$user ) {
			
      $kode = $request->input('idMitrakerja');
                    $mitra->Where('mitra.idMitrakerja', $kode);
        } );

				$mitra = $mitra->select([
                    'mitra.BRANCH_CODE','mitra.NAMA_INSTANSI','mitra.idMitrakerja','mitra.segmen','mitra.nama_uker','mitra.alamat',
                    , \DB::Raw(" case when mitra.kode is not null then 2 else 1 end as new_order ")
                ]);

        \Log::info($mitra->toSql());

        return $mitra;
    }

}
