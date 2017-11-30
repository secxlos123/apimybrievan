<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DbwsRest;

class Dropbox extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = '';

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
    protected $fillable = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function insertDropbox($data) {
        \Log::info($data);
        try {
            $data_dropbox = DbwsRest::setHeaders(['Content-Type'=>'application/x-www-form-urlencoded'])->setBody($data)->post('form_params');

            return $data_dropbox;
        } catch (Exception $e) {
            throw new \Exception( "Error Processing Request", 1 );
        }
    } 
}
