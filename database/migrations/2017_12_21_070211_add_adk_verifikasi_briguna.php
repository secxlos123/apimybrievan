<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdkVerifikasiBriguna extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('briguna', function (Blueprint $table) {
            $table->integer('is_verified')->nullable()->default(0);
            $table->text('catatan_kk')->nullable();
            $table->text('catatan_ktp')->nullable();
            $table->text('catatan_couple_ktp')->nullable();
            $table->text('catatan_npwp')->nullable();
            $table->text('catatan_sk_awal')->nullable();
            $table->text('catatan_sk_akhir')->nullable();
            $table->text('catatan_skpu')->nullable();
            $table->text('catatan_rekomendasi')->nullable();
            $table->text('catatan_gaji')->nullable();
            $table->integer('flag_kk')->nullable()->default(0);
            $table->integer('flag_ktp')->nullable()->default(0);
            $table->integer('flag_couple_ktp')->nullable()->default(0);
            $table->integer('flag_npwp')->nullable()->default(0);
            $table->integer('flag_sk_awal')->nullable()->default(0);
            $table->integer('flag_sk_akhir')->nullable()->default(0);
            $table->integer('flag_skpu')->nullable()->default(0);
            $table->integer('flag_rekomendasi')->nullable()->default(0);
            $table->integer('flag_slip_gaji')->nullable()->default(0);
        });
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
