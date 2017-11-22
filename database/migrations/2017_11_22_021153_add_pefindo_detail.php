<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPefindoDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eforms', function (Blueprint $table) {
            $table->integer('pefindo_score')->default(0)->nullable();
            $table->integer('is_screening')->default(0)->nullable();
            $table->text('uploadscore')->nullable();
            $table->text('ket_risk')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eforms', function (Blueprint $table) {
            $table->dropColumn(['pefindo_score', 'is_screening', 'uploadscore', 'ket_risk']);
        });
    }
}
