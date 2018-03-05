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
            $table->text('SK_AWAL')->nullable()->change();
            $table->text('SK_AKHIR')->nullable()->change();
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
            $table->text('SK_AWAL')->nullable()->change();
            $table->text('SK_AKHIR')->nullable()->change();
       });
    }
}
