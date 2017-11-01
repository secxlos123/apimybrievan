<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldnameLkn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visit_reports', function (Blueprint $table) {
            $table->renameColumn('use_reason','use_reason_name');
            $table->renameColumn('use_reason_id','use_reason');
            $table->string('salary_slip')->nullable();
            $table->string('proprietary')->nullable();
            $table->string('building_permit')->nullable();
            $table->string('kpp_type_name')->nullable();
            $table->string('type_financed_name')->nullable();
            $table->string('economy_sector_name')->nullable();
            $table->string('project_list_name')->nullable();
            $table->string('program_list_name')->nullable();
            $table->string('legal_bussiness_document')->nullable();
            $table->string('license_of_practice')->nullable();
            $table->string('work_letter')->nullable();
            $table->string('family_card')->nullable();
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
            $table->renameColumn('use_reason','use_reason_id');
            $table->renameColumn('use_reason_name','use_reason');
            $table->dropColumn('kpp_type_name');
            $table->dropColumn('type_financed_name');
            $table->dropColumn('economy_sector_name');
            $table->dropColumn('project_list_name');
            $table->dropColumn('program_list_name');
            $table->dropColumn('salary_slip');
            $table->dropColumn('proprietary');
            $table->dropColumn('building_permit');
            $table->dropColumn('legal_bussiness_document');
            $table->dropColumn('license_of_practice');
            $table->dropColumn('work_letter');
            $table->dropColumn('family_card');


        });
    }
}
