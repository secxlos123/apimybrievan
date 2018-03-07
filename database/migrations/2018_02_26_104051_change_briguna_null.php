<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBrigunaNull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('briguna', function (Blueprint $table) {
            $table->text('SK_AWAL')->nullable();
            $table->text('SK_AKHIR')->nullable();
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('briguna', function (Blueprint $table) {
            $table->dropColumn('SK_AWAL');
            $table->dropColumn('SK_AKHIR');
       });
    }
}
