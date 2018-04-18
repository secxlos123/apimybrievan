<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MitraApproval extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create( 'mitra_approval', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->text( 'fasilitas_jasa' );
            $table->text( 'daftar_ijin' );
            $table->text( 'id_approval' );
            $table->text( 'id_header' );
        } );
		
		 Schema::create( 'mitra_approval_simpanan', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->text( 'fasilitas_jasa' );
            $table->text( 'jenis_simpanan' );
            $table->text( 'no_rekening' );
            $table->text( 'rata_saldo' );
            $table->text( 'no_cif' );
            $table->text( 'rata_mutasi' );
            $table->text( 'nama_pemilik_rekening' );
            $table->text( 'jumlah_simpanan' );
            $table->text( 'id_approval' );
        } );
		Schema::create( 'mitra_approval_fasilitas', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->text( 'fasilitas_jasa' );
            $table->text( 'jenis_simpanan' );
            $table->text( 'total_os' );
            $table->text( 'presentase_npl' );
            $table->text( 'os_pl' );
            $table->text( 'jumlah_debitur' );
            $table->text( 'os_npl' );
            $table->text( 'jumlah_debitur_pl' );
            $table->text( 'jumlah_debitur_npl' );
            $table->text( 'id_approval' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'mitra_approval' );
        Schema::dropIfExists( 'mitra_approval_simpanan' );
        Schema::dropIfExists( 'mitra_approval_fasilitas' );
    }
}
