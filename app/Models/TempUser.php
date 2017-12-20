<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use File;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class TempUser extends Model implements AuditableContract
{
    use Auditable;
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
		'user_id', 'city_id', 'name', 'company_name', 'email', 'phone', 'mobile_phone', 'image', 'address', 'summary'
    ];

    /**
     * Get user temp-avatar image url.
     *
     * @return string
     */
    public function getImageAttribute( $value )
    {
        if( File::exists( 'uploads/temp-avatars/' . $value ) ) {
            $image = url( 'uploads/temp-avatars/' . $value );
        } else {
            $image = url( 'img/avatar.jpg' );
        }
        return $image;
    }

    /**
     * Set user temp-avatar image.
     *
     * @return void
     */
    public function setImageAttribute( $image )
    {
        $path = public_path( 'uploads/temp-avatars/' );
        if ( ! empty( $this->attributes[ 'image' ] ) ) {
            File::delete( $path . $this->attributes[ 'image' ] );
        }

        $filename = time() . '.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'image' ] = $filename;
    }

    /**
     * Get parent user of user detail.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo( User::class, 'user_id' );
    }

    /**
     * Get parent user of user detail.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo( City::class, 'city_id' );
    }
}
