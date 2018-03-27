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
								$branchcis ='';
								$branchcis2 = '';
								if(strlen($BRANCH_CODE)=='5'){
									$branchcis = $BRANCH_CODE;
									$k = strlen($BRANCH_CODE);
									$branchut2 = '';
									try{
									for($l=$k;$l<5;$l++){
										if(substr($BRANCH_CODE,0,$l+1)!='0'){
											$branchut2 = 'lempar';
										}
									}
									}catch($branchut2=='lempar'){
										$branchcis2 = substr($BRANCH_CODE,$l,5);
									}
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
										$branchcis2 = $BRANCH_CODE;
								}
								\Log::info($branchcis);
								$mitra->Where('BRANCH_CODE', $branchcis)->orWhere('BRANCH_CODE',$branchcis2);
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
