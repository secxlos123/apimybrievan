<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingFieldProperties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->bigInteger('staff_id')->nullable();
            $table->string('staff_name')->nullable();
            $table->enum('status', ['waiting', 'new', 'approved','rejected'])->default('new')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS developer_properties_view_table');
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('staff_id');
            $table->dropColumn('staff_name');
            $table->dropColumn('status');
        });
    }
}
