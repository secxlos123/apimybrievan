<?php

namespace App\Models;
use File;

use Illuminate\Database\Eloquent\Model;

class ApkLogs extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $table = 'apk_logs';

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'version_type', 'version_number', 'file_name'
    ];
    /**
     * [$appends description]
     * @var [type]
     */
    protected $appends = [ 'link'
    ];

    /**
     * Get the link apk.
     *
     * @return string
     */
    public function getLinkAttribute()
    {
        if( ! empty( $this->file_name ) ) {

            $image = 'uploads/apk/' . $this->apk_type . '/' . $this->file_name;
            if( File::exists( public_path( $image ) ) ) {
                $path = $image;
            }
        }
        return url( $path );
    }
}
