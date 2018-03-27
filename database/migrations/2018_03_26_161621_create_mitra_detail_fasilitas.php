<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitraDetailFasilitas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'mitra_detail_fasilitas', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->text( 'jenis_pengajuan' );
            $table->text( 'fasilitas_bank' );
            $table->text( 'ijin_perinsip' );
            $table->text( 'upload_ijin' );
            $table->text( 'upload_fasilitas_bank' );
            $table->text( 'daftar_ijin' );
            $table->text( 'id_header' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::dropIfExists( 'mitra_detail_fasilitas' );
    }
}







