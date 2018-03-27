<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitraDetailData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'mitra_detail_data', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->text( 'deskripsi_mitra' );
            $table->text( 'hp_mitra' );
            $table->text( 'bendaharawan_mitra' );
            $table->text( 'telp_bendaharawan_mitra' );
            $table->text( 'hp_bendaharawan_mitra' );
            $table->text( 'email' );
            $table->text( 'jml_pegawai' );
            $table->text( 'thn_pegawai' );
            $table->text( 'tgl_pendirian' );
            $table->text( 'akta_pendirian' );
            $table->text( 'akta_perubahan' );
            $table->text( 'npwp_usaha' );
            $table->text( 'laporan_keuangan' );
            $table->text( 'legalitas_perusahaan' );
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
        Schema::dropIfExists( 'mitra_detail_data' );
    }
}









