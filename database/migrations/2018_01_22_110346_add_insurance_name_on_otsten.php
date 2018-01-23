<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInsuranceNameOnOtsten extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ots_tens', function (Blueprint $table) {
            $table->string('insurance_company_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ots_tens', function (Blueprint $table) {
            $table->dropColumn('insurance_company_name');
        });
    }
}
