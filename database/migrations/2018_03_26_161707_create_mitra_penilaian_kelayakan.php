<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitraPenilaianKelayakan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'mitra_penilaian_kelayakan', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->text( 'hasil_penilaian' );
            $table->text( 'tgl_hasil' );
            $table->text( 'rekomendasi_unit' );
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
        Schema::dropIfExists( 'mitra_penilaian_kelayakan' );
    }
}





