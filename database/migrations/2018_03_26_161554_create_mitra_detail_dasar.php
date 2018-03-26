<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitraDetailDasar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'mitra_detail_dasar', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->text( 'jenis_mitra' );
            $table->text( 'induk_mitra' );
            $table->text( 'anak_perusahaan_wilayah' );
            $table->text( 'anak_perusahaan_kabupaten' );
            $table->text( 'no_telp_mitra' );
            $table->text( 'id_mitra' );
            $table->text( 'id_header' );
            $table->text( 'golongan_mitra' );
            $table->text( 'status' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
          Schema::dropIfExists( 'mitra_detail_dasar' );
    }
}










