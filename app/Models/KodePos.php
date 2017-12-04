<?php

namespace App\Models;;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;
use Sentinel;
use Asmx;
use RestwsHc;
use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;



class KodePos extends Model
{
	
	protected $table = 'tbl_kodepos';
	
	  public function scopeFilter( $query, Request $request )
    {
      $kodepos = $query->where( function( $kodepos ) use( $request, &$user ) {
			
      $kode = $request->input('key');
                    $kodepos->Where('tbl_kodepos.postal_code','like', $kode.'%');
					$kodepos->orWhere('tbl_kodepos.postal_code','like', $kode.'%');
					$kodepos->orderByRaw('tbl_kodepos.postal_code DESC');
        } );

				$kodepos = $kodepos->select([
                    'postal_code',
         		 \DB::Raw(" case when tbl_kodepos.postal_code is not null then 2 else 1 end as new_order ")
                ]);

        \Log::info($kodepos->toSql());
        return $kodepos;

    }
}