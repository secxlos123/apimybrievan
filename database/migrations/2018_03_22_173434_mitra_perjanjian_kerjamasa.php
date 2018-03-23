<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MitraPerjanjianKerjamasa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('mitra_perjanjian', function (Blueprint $table) {
             $table->increments('id');
            $table->text('perjanjian_layanan');
            $table->text('jenis_perjanjian');
            $table->text('judul_perjanjian');
            $table->text('deskripsi_perjanjian');
            $table->text('signer_mitra');
            $table->text('nomor_notaril');
            $table->text('nomor_perjanjian_bri');
            $table->text('nomor_perjanjian_ketiga');
            $table->text('tgl_perjanjian');
            $table->text('tgl_berakhir_perjanjian');
            $table->text('tgl_perjanjian_backdate');
            $table->text('tgl_register');
            $table->text('penilaian_mitra_register_radio');
            $table->text('penilaian_mitra_kelayakan_radio');
            $table->text('penilaian_mitra_rks_radio');
            $table->text('pemutus_name_perjanjian');
            $table->text('pemeriksa_perjanjian');
            $table->text('jabatan_perjanjian');
            $table->text('jabatan_pemeriksa_perjanjian');
            $table->text('upload_perjanjian');
            $table->text('id_header');
		  });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marketing_notes');
    }
}
