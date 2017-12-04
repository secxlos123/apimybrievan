<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKodepos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create( 'tbl_kodepos', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->text( 'postal_code' )->nullable();
            $table->text( 'Kelurahan' )->nullable();
            $table->text( 'Kecamatan' )->nullable();
            $table->text( 'Kota' )->nullable();
            $table->text( 'Propinsi' )->nullable();
            $table->text( 'code' )->nullable();
            $table->text( 'district_id' )->nullable();
            $table->text( 'city_id' )->nullable();
            $table->text( 'province_id' )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
          Schema::dropIfExists( 'tbl_kodepos' );
    }
}
