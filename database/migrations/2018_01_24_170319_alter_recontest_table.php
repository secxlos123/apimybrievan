<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRecontestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recontests', function (Blueprint $table) {
            $table->string('source')->nullable();
            $table->string("income")->nullable();
            $table->string("income_salary")->nullable();
            $table->string("income_allowance")->nullable();
            $table->string("source_income")->nullable();
            $table->string("couple_salary")->nullable();
            $table->string("couple_other_salary")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recontests', function (Blueprint $table) {
            $table->dropColumn(["source", "income", "income_salary", "income_allowance", "source_income", "couple_salary", "couple_other_salary"]);
        });
    }
}
