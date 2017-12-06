<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldOnOtsEnvironmentsForClas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ots_environments', function (Blueprint $table) {
            $table->string('designated_pln')->default('0');
            $table->string('designated_phone')->default('0');
            $table->string('designated_pam')->default('0');
            $table->string('designated_telex')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ots_environments', function (Blueprint $table) {
             $table->dropColumn(['designated_pln','designated_phone','designated_pam','designated_telex']);
        });
    }
}
