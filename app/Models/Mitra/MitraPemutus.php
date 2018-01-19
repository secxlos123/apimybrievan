<?php

namespace App\Models\Mitra;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Asmx;

class MitraPemutus extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'approval_pemutus';

    /**
     * Disabling timestamp feature.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	 
    protected $fillable = [  			
						'pemutus','jabatan','pemeriksa','jabatan_pemeriksa','status','id_approval'];
	
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'id_gimmick' ];

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getIdAttribute( $value )
    {
        return $this->id_gimmick;
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public static function create( $data ) {
	  try {        

        $gimmick = ( new static )->newQuery()->create($data);
            return $gimmick;
     } catch (Exception $e) {
            return $e;    
    }

    }

 
    public function scopeFilter( $query, Request $request )
    {
        $sort = $request->input('sort') ? explode('|', $request->input('sort')) : ['id', 'asc'];
        $user = \RestwsHc::getUser();

        if ( $sort[0] == "id" ) {
            $sort = ['id', 'asc'];
        }

		$hari_ini = date("Y/m/d") ;
		
		 $dir = $query->where( function( $dir ) use( $request,$hari_ini ) {
            if ( $request->has('gimmick_name') ) {
                $dir = $dir->where('gimmick.gimmick_name', $request->input('gimmick_name'));
			} 
			
		\Log::info($hari_ini);
			
//			if ( $request->has('tgl_mulai')&&$request->has('tgl_berakhir') ) {
                $dir = $dir->where('gimmick.tgl_mulai','<', $hari_ini);
                $dir = $dir->where('gimmick.tgl_berakhir','>', $hari_ini);
//			}
        } );
		 $dir = $dir->join('gimmick_detail', 'gimmick_detail.id_header', '=', 'gimmick.id_header');
        $dir = $dir->orderBy('gimmick.'.$sort[0], $sort[1]);

        \Log::info($dir->toSql());
        \Log::info($dir->getBindings());

        return $dir;
    }

}
