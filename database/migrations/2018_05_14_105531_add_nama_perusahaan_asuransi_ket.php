<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNamaPerusahaanAsuransiKet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('briguna', function (Blueprint $table) {
            $table->string('nama_program_promo_ket')->nullable();
            $table->string('nama_perusahaan_asuransi_ket')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('briguna', function (Blueprint $table) {
            $table->dropColumn(['nama_program_promo_ket', 'nama_perusahaan_asuransi_ket']);
        });
    }
}
