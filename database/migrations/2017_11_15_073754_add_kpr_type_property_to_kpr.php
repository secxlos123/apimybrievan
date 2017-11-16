<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKprTypePropertyToKpr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpr', function (Blueprint $table) {
            $table->string('kpr_type_property')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kpr', function (Blueprint $table) {
            $table->dropColumn(['kpr_type_property']);
        });
    }
}
