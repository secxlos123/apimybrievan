<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCollateralManagerToTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->integer('manager_id')->nullable();
            $table->string('manager_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collaterals', function (Blueprint $table) {
            $table->dropColumn(['manager_id', 'manager_name']);
        });
    }
}
