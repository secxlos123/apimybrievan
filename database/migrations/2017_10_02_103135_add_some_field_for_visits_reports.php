<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeFieldForVisitsReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @todo  removed
     * @return void
     */
    public function up()
    {
        Schema::table( 'visit_reports', function ( Blueprint $table ) {
            $table->integer( 'npwp' )->nullable();
            $table->string( 'income_type' )->nullable();
            $table->string( 'couples_monthly_salary' )->nullable();
            $table->string( 'other_income_couples' )->nullable();
            $table->string( 'kpp_type' )->nullable();
            $table->integer( 'financed_type' )->nullable();
            $table->integer( 'sector_economy' )->nullable();
            $table->integer( 'project_value' )->nullable();
            $table->integer( 'program_value' )->nullable();
            $table->integer( 'sub_third_party_value' )->nullable();
            $table->string( 'family_name' )->nullable();
            $table->integer( 'use_reason_value' )->nullable();
            $table->string( 'use_reason' )->nullable();
            $table->integer( 'id_prescreening')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
