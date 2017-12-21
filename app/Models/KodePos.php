<?php

namespace App\Models;;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class KodePos extends Model
{
	  protected $table = 'tbl_kodepos';
	  public function scopeFilter($query, $request) {
        $kodepos = $query->where( function( $kodepos ) use( $request, &$user ) {
          $kode = $request['key'];
          $kodepos->Where('tbl_kodepos.postal_code','like', $kode.'%');
					$kodepos->orWhere('tbl_kodepos.postal_code','like', $kode.'%');
					$kodepos->orderByRaw('tbl_kodepos.postal_code DESC');
        });

			
				$kodepos = $kodepos->select([
          '*'
        ]);

        \Log::info($kodepos->toSql());
        return $kodepos;

    }
}