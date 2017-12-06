<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingOnTableKpr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpr', function (Blueprint $table) {
            $table->integer('property_type')->nullable();
            $table->string('property_type_name')->nullable();
            $table->integer('property_item')->nullable();
            $table->string('property_item_name')->nullable();
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
            $table->dropColumn(['property_type','property_type_name','property_item','property_item_name']);
        });
    }
}
