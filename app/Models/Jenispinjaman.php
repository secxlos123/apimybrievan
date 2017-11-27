<?php namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser as Authenticatable;

class Jenispinjaman extends Authenticatable  {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jenis_pinjaman';
}
