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
	
						 if( $request->has( 'BRANCH_CODE' ) ) {
								$BRANCH_CODE = $request->input('BRANCH_CODE');
									for($i=0;$i<5;$i++){
										$cek = substr($BRANCH_CODE,$i,1);
										if($cek!=0){
											$branchcis = substr($BRANCH_CODE,$i,4);
											$i = 5;
										}
									}
		 						return $BRANCH_CODE;die();
								$mitra->Where('BRANCH_CODE', $BRANCH_CODE);
						 }
						 
						 if( $request->has( 'search' ) ) {
		 						$mitra->whereRaw('LOWER("NAMA_INSTANSI") LIKE ? ',['%'.trim(strtolower($request->input('search'))).'%']);
						 }
						$mitra->whereRaw('LOWER("NAMA_INSTANSI") LIKE ? ',['%'.trim(strtolower($request->input('search'))).'%']);

//paging
        } );
			
	//			$mitra->where('LOWER(NAMA_INSTANSI)','like','%LOWER('.$request->input('search').')%');
	if( $request->has( 'sort' ) ) {
				$mitra->orderBy($request->input('sort'), 'ASC');
	}
				$mitra = $mitra->select([
                    '*',
                     \DB::Raw(" case when mitra.kode is not null then 2 else 1 end as new_order ")
                ]);
		
        \Log::info($mitra->toSql());

        return $mitra;
    }

}
