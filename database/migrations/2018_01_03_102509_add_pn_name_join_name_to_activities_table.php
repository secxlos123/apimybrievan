<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPnNameJoinNameToActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marketing_activities', function (Blueprint $table) {
          $table->string('pn_name')->after('pn_join')->nullable();
          $table->string('join_name')->after('pn_join')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marketing_activities', function (Blueprint $table) {
          $table->dropColumn('join_name');
          $table->dropColumn('pn_name');
        });
    }
}
