<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAgamaDanAnalisa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('briguna', function ( Blueprint $table ) {
            $table->text('ket_agama')->nullable();
            $table->text('catatan_analisa')->nullable();
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
            $table->dropColumn(['ket_agama','catatan_analisa']);
        });
    }
}
