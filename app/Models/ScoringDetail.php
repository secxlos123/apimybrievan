<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use File;

class ScoringDetail extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'eforms';

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
        'ket_risk','uploadscore','pefindo_score','tujuan_penggunaan','jenis_pinjaman','mitra', 'nik', 'user_id', 'internal_id', 'ao_id', 'appointment_date', 'longitude', 'latitude', 'branch_id', 'product_type', 'prescreening_status', 'is_approved', 'pros', 'cons', 'additional_parameters', 'address'
		];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'user_id'
    ];

    /**
     * Get user avatar image url.
     *
     * @return string
     */

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getScoringAttribute( $value )
    {
        if( File::exists( 'uploads/prescreening/' . $this->user_id . '/' . $value ) ) {
            $image = url( 'uploads/prescreening/' . $this->user_id . '/' . $value );
        } else {
            $image = url( 'img/noimage.jpg' );
        }
        return $image;
    }


    public function setScoringAttribute( $image )
    {
        $path = public_path( 'uploads/prescreening/' . $this->user_id . '/' );
        if ( ! empty( $this->attributes[ 'uploadscore' ] ) ) {
            File::delete( $path . $this->attributes[ 'uploadscore' ] );
        }
		
		

	   if (!$image->getClientOriginalExtension()) {
            if ($image->getMimeType() == '.pdf') {
                $extension = '.pdf';
            }else{
                $extension = '.pdf';
            }
        }else{
            $extension = $image->getClientOriginalExtension();
        }
        // log::info('image = '.$image->getMimeType());
        $filename = $this->user_id . '-uploadscore.' . $extension;
        $image->move( $path, $filename );
        $this->attributes[ 'uploadscore' ] = $filename;
		
    }

 
}
