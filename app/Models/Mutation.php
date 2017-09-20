<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use File;

class Mutation extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'mutations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'visit_report_id', 'bank', 'number', 'file' ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [ 'visit_report' ];

    /**
     * Disabling timestamp feature.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Get mutation file url.
     *
     * @return string
     */
    public function getFileAttribute( $value )
    {
        $image = url( 'img/noimage.jpg' );
        if(  ! empty( $value ) ) {
            if( File::exists( 'uploads/eforms/' . $this->visit_report->eform_id . '/visit_report/' . $value ) ) {
                $image = url( 'uploads/eforms/' . $this->visit_report->eform_id . '/visit_report/' . $value );
            }
        }
        return $image;
    }

    /**
     * Set mutation file proof.
     *
     * @return void
     */
    public function setFileAttribute( $file )
    {
        $path = public_path( 'uploads/eforms/' . $this->visit_report->eform_id . '/visit_report/' );
        if ( ! empty( $this->attributes[ 'file' ] ) ) {
            File::delete( $path . $this->attributes[ 'file' ] );
        }

        $filename = 'mutation-' . $this->attributes[ 'bank' ] . '.' . $file->getClientOriginalExtension();
        $file->move( $path, $filename );
        $this->attributes[ 'file' ] = $filename;
    }

    /**
     * The directories belongs to broadcasts.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function visit_report()
    {
        return $this->belongsTo( VisitReport::class, 'visit_report_id' );
    }
}
