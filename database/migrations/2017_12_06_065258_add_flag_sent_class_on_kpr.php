<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFlagSentClassOnKpr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpr', function (Blueprint $table) {
            $table->boolean('is_sent')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS collateral_view_table');
        Schema::table('kpr', function (Blueprint $table) {
            $table->dropColumn('is_sent');
        });
    }
}
