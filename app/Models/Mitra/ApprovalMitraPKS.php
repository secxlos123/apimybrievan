<?php

namespace App\Models\Mitra;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Asmx;

class ApprovalMitraPKS extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'mitra_approval';

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
					'fasilitas_jasa','daftar_ijin','id_header'];
	
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'id' ];

    /**
     * Get AO detail information.
     *
     * @return string
     */
    public function getIdAttribute( $value )
    {
        return $this->id;
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

}
