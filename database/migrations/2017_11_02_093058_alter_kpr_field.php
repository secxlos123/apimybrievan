<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterKprField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kpr', function (Blueprint $table) {
            $table->string('developer_name')->nullable();
            $table->string('property_name')->nullable();
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
            $table->dropColumn('developer_name');
            $table->dropColumn('property_name');
        });
    }
}
