<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserNameAndPositionToEform extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eforms', function (Blueprint $table) {
            $table->string('ao_name')->nullable();
            $table->string('ao_position')->nullable();
            $table->string('pinca_name')->nullable();
            $table->string('pinca_position')->nullable();
            $table->string('prescreening_name')->nullable();
            $table->string('prescreening_position')->nullable();
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
            $table->dropColumn(['ao_name', 'ao_position', 'pinca_name', 'pinca_position', 'prescreening_name', 'prescreening_position']);
        });
    }
}
