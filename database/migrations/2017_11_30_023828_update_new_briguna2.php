<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNewBriguna2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( 'briguna', function ( Blueprint $table ) {
            $table->text( 'NPWP_nasabah' );
            $table->text( 'NIP' );
            $table->text( 'Status_Pekerjaan' );
            $table->text( 'Nama_atasan_Langsung' );
            $table->text( 'Jabatan_atasan' );
            $table->text( 'KK' );
            $table->text( 'SLIP_GAJI' );
            $table->text( 'SK_AWAL' );
            $table->text( 'SK_AKHIR' );
            $table->text( 'REKOMENDASI' );
            $table->text( 'SKPG' );
            $table->double( 'request_amount' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
