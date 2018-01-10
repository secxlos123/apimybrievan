<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDebiturBriguna extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('briguna', function (Blueprint $table) {
            $table->text('usia_mpp')->nullable();
            $table->text('lama_menetap')->nullable();
            $table->text('kode_pos')->nullable();
            $table->text('kode_pos_dom')->nullable();
            $table->text('kelurahan')->nullable();
            $table->text('kelurahan_dom')->nullable();
            $table->text('kecamatan')->nullable();
            $table->text('kecamatan_dom')->nullable();
            $table->text('kota')->nullable();
            $table->text('kota_dom')->nullable();
            $table->text('perjanjian_pisah_harta')->nullable();
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
            $table->dropColumn([
                'usia_mpp','lama_menetap','kode_pos','kode_pos_dom',
                'kelurahan','kelurahan_dom','kecamatan','kecamatan_dom',
                'kota','kota_dom','perjanjian_pisah_harta'
            ]);
        });
    }
}
