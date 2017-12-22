<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldNameOnApprovalDataChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approval_data_changes', function (Blueprint $table) {
             $table->string('first_name',50)->nullable();
             $table->string('last_name',50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approval_data_changes', function (Blueprint $table) {
            $table->dropColumn(['first_name','last_name']);
        });
    }
}
