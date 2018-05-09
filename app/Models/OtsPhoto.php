<?php

namespace App\Models;

use File;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class OtsPhoto extends Model implements AuditableContract
{
	use Auditable;

	protected $fillable = [
		'id',
		'ots_other_id',
		'image_data'
	];
	public static $folder = '';

	/**
	 * Relation with otsOther
	 * @return \Illuminate\Database\Eloquent\BelongsTo
	 */
	public function otsOther() {
		return $this->belongsTo(OtsAnotherData::class, 'ots_other_id');
	}

	/**
	 * Global function for check file.
	 *
	 * @return string
	 */
	public function globalImageCheck($filename) {
		$path = 'img/noimage.jpg';
		if (!empty($filename)) {
			$image = 'uploads/collateral/other/' . $this->ots_other_id . '/' . $filename;
			if (File::exists(public_path($image))) {
				// $path = $image;
				$path = 'files/collateral/other/' . $this->ots_other_id . '/' . $filename;
			}
		}

		return url($path);
	}

	/**
	 * Global function for set image attribute.
	 *
	 * @return void
	 */
	public function globalSetImageAttribute($image, $attribute, $callbackPosition = null) {
		if ($image != "") {
			$this->attributes[$attribute] = $image;

			if (gettype($image) != "string") {
				$return = $this->globalSetImage($image, $attribute, $callbackPosition);
				if ($return) {
					$this->attributes[$attribute] = $return;
				}
			}

		}
	}

	/**
	 * Global function for set image.
	 *
	 * @return void
	 */
	public function globalSetImage($image, $attribute, $callbackPosition = null) {
		$doFunction = true;

		if ($callbackPosition) {
			$doFunction = isset($this->attributes[$attribute]);
		}
		if (isset($this->attributes[$attribute]) && gettype($image) == 'object') {
			$path = public_path('uploads/collateral/other/' . $this->ots_other_id . '/');
			if (!empty($this->attributes[$attribute])) {
				File::delete($path . $this->attributes[$attribute]);
			}

			$extension = 'png';

			if (!$image->getClientOriginalExtension()) {
				if ($image->getMimeType() == 'image/jpg') {
					$extension = 'jpg';
				} elseif ($image->getMimeType() == 'image/jpeg') {
					$extension = 'jpeg';
				}
			} else {
				$extension = $image->getClientOriginalExtension();
			}
			$num =  random_int(1,9)*(int)$this->ots_other_id;
			$filename = $this->ots_other_id . '-' . $attribute.'-'.$num.'.' . $extension;
			$image->move($path, $filename);
			return $filename;
		} else {
			return null;
		}
	}

	/**
     * Get user collateral_binding_doc.
     *
     * @return string
     */
    public function getImageDataAttribute( $value )
    {
        return $this->globalImageCheck( $value );
    }

    /**
     * Set customer collateral_binding_doc.
     *
     * @return void
     */
    public function setImageDataAttribute( $image )
    {
        $this->globalSetImageAttribute( $image, 'image_data' );
    }
}
