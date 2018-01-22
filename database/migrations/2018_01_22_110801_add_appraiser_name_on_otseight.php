<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAppraiserNameOnOtseight extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ots_eights', function (Blueprint $table) {
            $table->dropColumn('nilai pengikatan => binding_value');
            $table->string('independent_appraiser_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ots_eights', function (Blueprint $table) {
            $table->dropColumn('independent_appraiser_name');
            $table->string('nilai pengikatan => binding_value')->nullable();
        });
    }
}
