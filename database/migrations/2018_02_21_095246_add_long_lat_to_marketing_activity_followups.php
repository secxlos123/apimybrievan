<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLongLatToMarketingActivityFollowups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marketing_activity_followups', function (Blueprint $table) {
          $table->string('longitude')->nullable();
          $table->string('latitude')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marketing_activity_followups', function (Blueprint $table) {
          $table->dropColumn('longitude');
          $table->dropColumn('latitude');
        });
    }
}
