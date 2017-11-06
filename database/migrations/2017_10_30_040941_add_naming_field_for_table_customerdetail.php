<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNamingFieldForTableCustomerdetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_details', function (Blueprint $table) {
            $table->string('citizenship_name')->nullable();
            $table->string('job_type_name')->nullable();
            $table->string('job_field_name')->nullable();
            $table->string('job_name')->nullable();
            $table->string('position_name')->nullable();
            //$table->string('cif_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_details', function (Blueprint $table) {
            $table->dropColumn('citizenship_name');
            $table->dropColumn('job_type_name');
            $table->dropColumn('job_name');
            $table->dropColumn('position_name');
            $table->dropColumn('job_field_name');
            //$table->dropColumn('cif_number');
        });
    }
}
