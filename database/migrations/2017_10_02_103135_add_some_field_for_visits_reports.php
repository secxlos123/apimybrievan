<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeFieldForVisitsReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visit_reports', function (Blueprint $table) {
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
        Schema::table( 'visit_reports', function ( Blueprint $table ) {
            $table->dropColumn( 'npwp' );
            $table->dropColumn( 'income_type' );
            $table->dropColumn( 'couples_monthly_salary' );
            $table->dropColumn( 'other_income_couples' );
            $table->dropColumn( 'kpp_type' );
            $table->dropColumn( 'financed_type' );
            $table->dropColumn( 'sector_economy' );
            $table->dropColumn( 'project_value' );
            $table->dropColumn( 'program_value' );
            $table->dropColumn( 'sub_third_party_value' );
            $table->dropColumn( 'family_name' );
            $table->dropColumn( 'use_reason_value' );
            $table->dropColumn( 'use_reason' );
            $table->dropColumn( 'id_prescreening');
        } );
    }
}
