<?php

namespace App\Models;;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;
use Sentinel;
use Asmx;
use RestwsHc;
use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;



class Mitra2 extends Model
{
	
	protected $table = 'mitra';
	
	  public function scopeFilter( $query, Request $request )
    {
      $mitra = $query->where( function( $mitra ) use( $request, &$user ) {
			
      $kode = $request->input('key');
                    $mitra->Where('mitra.NAMA_INSTANSI','like', $kode.'%');
					$mitra->orWhere('mitra.idMitrakerja','like', $kode.'%');
        } );

				$mitra = $mitra->select([
                    '*',
         		 \DB::Raw(" case when mitra.kode is not null then 2 else 1 end as new_order ")
                ]);

        \Log::info($mitra->toSql());

        return $mitra;

    }
}