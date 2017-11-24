<?php namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;

class Mitra extends Authenticatable  {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'mitra';
}
