<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use File;

class Recontest extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'eform_id', 'purpose_of_visit', 'pros', 'cons', 'ao_recommendation', 'ao_recommended', 'pinca_recommendation', 'pinca_recommended', 'expired_date', 'documents', 'mutations'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [ 'documents' => 'array', 'mutations' => 'array' ];

    /**
     * The relation to EForm.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function eform()
    {
        return $this->belongsTo( EForm::class, 'eform_id' );
    }

    /**
     * Generate array upload data
     *
     * @return void
     **/
    public function generateArrayData( $request, $field )
    {
        $return = array();
        $eform = $this->eform;
        $path = 'uploads/' . $eform->nik . '/';
        $publicPath = public_path( $path );

        if ( !empty( $this->{$field} ) ) {
            foreach ($this->{$field} as $key => $value) {
                if ( isset($value[ 'image_name' ]) ) {
                    File::delete( $publicPath . $value[ 'image_name' ] );
                }
            }
        }

        foreach ($request as $key => $data) {
            $keyTarget = 'file';
            $name = $key;
            if ( $field == 'documents' ) {
                $keyTarget = 'document';
                $name = $data[ 'name' ];
            }

            $image = $this->globalSetImage( $eform, $publicPath, ($name . '-' . $field), $data[ $keyTarget ] );
            unset( $data[ $keyTarget ] );

            if ( $image ) {
                $data[ 'image_name' ] = $image;
                $data[ 'image' ] = $this->globalImageCheck( $path . $image );
                $return[ $key ] = $data;
            }
        }

        $this->{$field} = $return;
        $this->save();
    }

    /**
     * Global function for set image.
     *
     * @return string
     */
    public function globalSetImage( $eform, $path, $name, $image )
    {
        $filename = null;
        if ( gettype($image) == 'object' ) {
            $extension = 'png';

            if ( !$image->getClientOriginalExtension() ) {
                if ( $image->getMimeType() == 'image/jpg' ) {
                    $extension = 'jpg';
                } elseif ( $image->getMimeType() == 'image/jpeg' ) {
                    $extension = 'jpeg';
                }
            } else {
                $extension = $image->getClientOriginalExtension();
            }

            $filename = $eform->id . '-' . $name . '.' . $extension;
            $image->move( $path, $filename );
        }

        return $filename;
    }

    /**
     * Global function for check file.
     *
     * @return string
     */
    public function globalImageCheck( $filename )
    {
        $path =  'img/noimage.jpg';
        if( ! empty( $filename ) ) {
            \Log::info("===================================================recontest");
            \Log::info($filename);
            \Log::info(public_path( $filename ));
            \Log::info("recontest===================================================");
            if( File::exists( public_path( $filename ) ) ) {
                $path = $filename;
            }
        }

        return url( $path );
    }
}
