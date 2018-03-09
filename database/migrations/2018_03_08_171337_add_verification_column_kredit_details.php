<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVerificationColumnKreditDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('kartu_kredit_details', function (Blueprint $table) {
            $table->string('verification_code')->nullable();
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
            $table->dropColumn('verification_code');
        });
    }
}
