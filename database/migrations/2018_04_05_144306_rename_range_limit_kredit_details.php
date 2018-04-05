<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameRangeLimitKreditDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kartu_kredit_details', function (Blueprint $table) {
            //
            $table->renameColumn('limit_tertinggi','range_limit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('kartu_kredit_details', function (Blueprint $table) {
            $table->renameColumn('range_limit','limit_tertinggi');
        });
    }
}
