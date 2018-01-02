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
	protected $table = 'mitra';
	
	  public function scopeFilter( $query, Request $request )
    {
		
	  $kode= '';
        $mitra = $query->where( function( $mitra ) use( $request, &$user ) {
		
						$BRANCH_CODE = $request->input('BRANCH_CODE');
						
						 $mitra->Where('BRANCH_CODE', $BRANCH_CODE);
        } );
			
				//$mitra->where('LOWER(NAMA_INSTANSI)','like','%LOWER('.$kode.')%');
				$mitra->orderBy('NAMA_INSTANSI', 'ASC');
				$mitra = $mitra->select([
                    '*',
                     \DB::Raw(" case when mitra.kode is not null then 2 else 1 end as new_order ")
                ]);
		
        \Log::info($mitra->toSql());

        return $mitra;
    }

}
