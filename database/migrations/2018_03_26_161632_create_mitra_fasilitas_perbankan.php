<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitraFasilitasPerbankan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create( 'mitra_detail_fasilitas_perbankan', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->text( 'fasilitas_lainnya' );
            $table->text( 'deskripsi_fasilitas_lainnya' );
            $table->text( 'nomor_pks_notaril' );
            $table->text( 'nomor_perjanjian_kerjasama_bri' );
            $table->text( 'nomor_perjanjian_kerjasama_ketiga' );
            $table->text( 'tgl_perjanjian_backdate' );
            $table->text( 'ijin_prinsip' );
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
       Schema::dropIfExists( 'mitra_detail_fasilitas_perbankan' );
    }
}










