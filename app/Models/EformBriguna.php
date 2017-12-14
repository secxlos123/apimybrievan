<?php 
namespace App\Models;;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\Request;
use Sentinel;
use Asmx;
use RestwsHc;
use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;


class EformBriguna extends Model
{
	
	protected $table = 'eforms';
	
	  public function scopeFilter( $query, Request $request )
    {
      $eforms = $query->where( function( $eforms ) use( $request, &$user ) {
			
      $kode = $request->input('id');
                    $eforms->Where('eforms.id', $kode);
        } );
				$eforms->join('briguna', 'eform_id', '=', 'eforms.id');
				$eforms = $eforms->select([
                    'briguna.*','eforms.*',
         		 \DB::Raw(" case when eforms.id is not null then 2 else 1 end as new_order ")
                ]);
        \Log::info($eforms->toSql());
        return $eforms;

    }
}