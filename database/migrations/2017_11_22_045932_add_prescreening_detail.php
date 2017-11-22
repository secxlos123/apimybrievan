<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrescreeningDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eforms', function (Blueprint $table) {
            $table->text('dhn_detail')->nullable();
            $table->text('sicd_detail')->nullable();
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
            $table->dropColumn(['dhn_detail', 'sicd_detail']);
        });
    }
}
