<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;
use Sentinel;
use Asmx;
use RestwsHc;
use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;

class Mitra3 extends Authenticatable  {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'mitra_relation';
	
	  public function scopeFilter( $query, Request $request )
    {
		
	  $kode= '';
        $mitra = $query->where( function( $mitra ) use( $request, &$user ) {
		
						$BRANCH_CODE = $request->input('BRANCH_CODE');
						 $mitra->Where('BRANCH_CODE', $BRANCH_CODE);
						$mitra->whereRaw('LOWER("NAMA_INSTANSI") LIKE ? ',['%'.trim(strtolower($request->input('search'))).'%']);

//paging
        } );
			
	//			$mitra->where('LOWER(NAMA_INSTANSI)','like','%LOWER('.$request->input('search').')%');
				$mitra->orderBy($request->input('sort'), 'ASC');
				$mitra = $mitra->select([
                    '*',
                     \DB::Raw(" case when mitra_relation.kode is not null then 2 else 1 end as new_order ")
                ]);
		
        \Log::info($mitra->toSql());

        return $mitra;
    }

}
