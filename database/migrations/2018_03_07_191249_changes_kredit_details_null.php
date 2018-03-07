<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangesKreditDetailsNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kartu_kredit_details', function (Blueprint $table) {
            $table->string('user_id')->nullable()->change();
            $table->string('hp')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('nama_ibu_kandung')->nullable();
            $table->string('status_pernikahan')->nullable();
            $table->string('jenis_kelamin')->nullable()->change();
            $table->string('tanggal_lahir')->nullable();
            $table->string('nama')->nullable()->change();
            $table->string('tempat_lahir')->nullable()->change();
            $table->string('telephone')->nullable()->change();
            $table->string('pendidikan')->nullable()->change();

            $table->string('pekerjaan')->nullable()->change();
            $table->string('tiering_gaji')->nullable()->change();

            $table->string('agama')->nullable()->change();
            $table->string('jenis_nasabah')->nullable()->change();
            $table->string('pilihan_kartu')->nullable()->change();
            $table->string('penghasilan_perbulan')->nullable()->change();
            $table->string('jumlah_penerbit_kartu')->nullable()->change();
            $table->string('limit_tertinggi')->nullable()->change();
            $table->string('image_npwp')->nullable()->change();
            $table->string('image_ktp')->nullable()->change();
            $table->string('image_slip_gaji')->nullable()->change();
            $table->string('image_nametag')->nullable()->change();
            $table->string('image_kartu_bank_lain')->nullable()->change();
            $table->string('eform_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kartu_kredit_details', function (Blueprint $table) {
            //
        });
    }
}
