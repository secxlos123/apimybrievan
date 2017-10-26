<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldOnCustomerDetailForLkn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visit_reports', function (Blueprint $table) {
            $table->renameColumn('result', 'visit_result');
            $table->enum('source', ['fixed', 'nonfixed'])->nullable();
            $table->renameColumn('income_type', 'income');
            $table->renameColumn('couples_monthly_salary', 'income_salary');
            $table->renameColumn('other_income_couples', 'income_allowance');
            $table->renameColumn('use_reason_value', 'use_reason_id');
            $table->renameColumn('financed_type', 'type_financed');
            $table->renameColumn('sector_economy', 'economy_sector');
            $table->renameColumn('project_value', 'project_list');
            $table->renameColumn('program_value', 'program_list');
            $table->renameColumn('sub_third_party_value', 'id_prescreening');
            $table->enum('recommended', ['yes', 'no'])->nullable();
            $table->longText('recommendation')->nullable();
            $table->renameColumn('npwp', 'npwp_number');
            $table->renameColumn('family_name', 'npwp');
            $table->string('legal_document')->nullable();
            $table->string('marrital_certificate')->nullable();
            $table->string('divorce_certificate')->nullable();
            $table->string('offering_letter')->nullable();
            $table->string('down_payment')->nullable();
            $table->string('building_tax')->nullable();

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
            $table->renameColumn('visit_result', 'result');
            $table->dropColumn('source');
            $table->renameColumn('income', 'income_type');
            $table->renameColumn('income_salary', 'couples_monthly_salary');
            $table->renameColumn('income_allowance', 'other_income_couples');
            $table->renameColumn('use_reason_id', 'use_reason_value');
            $table->renameColumn('type_financed', 'financed_type');
            $table->renameColumn('economy_sector', 'sector_economy');
            $table->renameColumn('project_list', 'project_value');
            $table->renameColumn('program_list', 'program_value');
            $table->renameColumn('id_prescreening', 'sub_third_party_value');
            $table->dropColumn('recommended');
            $table->dropColumn('recommendation');
            $table->renameColumn('npwp_number', 'npwp');
            $table->renameColumn('npwp', 'family_name');
            $table->dropColumn('legal_document');
            $table->dropColumn('marrital_certificate');
            $table->dropColumn('divorce_certificate');
            $table->dropColumn('offering_letter');
            $table->dropColumn('down_payment');
            $table->dropColumn('building_tax');

        });
    }
}
