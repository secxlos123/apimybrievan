<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeFieldOnCustomerDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_details', function (Blueprint $table) {
            $table->string('couple_salary')->nullable();
            $table->string('couple_other_salary')->nullable();
            $table->string('couple_loan_installment')->nullable();
            $table->string('emergency_name')->nullable();

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
            $table->dropColumn('couple_salary');
            $table->dropColumn('couple_other_salary');
            $table->dropColumn('couple_loan_installment');
            $table->dropColumn('emergency_name');

        });
    }
}
