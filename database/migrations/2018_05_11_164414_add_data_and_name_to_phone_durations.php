<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDataAndNameToPhoneDurations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::table('marketing_activity_phone_durations', function ( Blueprint $table ) {
             $table->string('date')->nullable();
             $table->string('name')->nullable();
         });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('marketing_activity_phone_durations', function (Blueprint $table) {
          $table->dropColumn(['date','name']);
      });
    }
}
