<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;

class OtsPhoto extends Model {
	use Auditable;

	protected $fillable = [
		'ots_other_id',
		'image_data',
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
				$path = $image;
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
		\Log::info("========================handling upload=============================");
		$base = $this->ots_other_id ? $this->ots_other_id : self::$folder;
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

			$filename = $this->collateral_id . '-' . $attribute . '.' . $extension;
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
