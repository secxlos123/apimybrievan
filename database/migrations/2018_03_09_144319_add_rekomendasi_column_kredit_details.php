<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRekomendasiColumnKreditDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kartu_kredit_details', function (Blueprint $table) {
            $table->string('catatan_rekomendasi_ao')->nullable();
            $table->string('catatan_rekomendasi_pinca')->nullable();
            $table->string('rekomendasi_limit_kartu')->nullable();
            $table->string('approval')->nullable();
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
            $table->dropColumn('catatan_rekomendasi_ao');
            $table->dropColumn('catatan_rekomendasi_pinca');
            $table->dropColumn('rekomendasi_limit_kartu');
            $table->dropColumn('approval');
        });
    }
}
