<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CRFieldLKN extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visit_reports', function (Blueprint $table) {
            $table->integer('title')->nullable();
            $table->integer('employment_status')->nullable();
            $table->integer('age_of_mpp')->nullable();
            $table->integer('loan_history_accounts')->nullable();
            $table->string('religion')->nullable();
            $table->string('office_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visit_reports', function (Blueprint $table) {
            $table->dropColumn(['title', 'employment_status', 'age_of_mpp', 'loan_history_accounts', 'religion', 'office_phone']);
        });
    }
}
