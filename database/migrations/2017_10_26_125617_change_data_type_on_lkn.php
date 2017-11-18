<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDataTypeOnLkn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visit_reports', function (Blueprint $table) {
            $table->string('npwp_number')->nullable()->change();
            $table->string('selling_price')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement("ALTER TABLE visit_reports ALTER npwp_number TYPE INTEGER USING 0 ");
        \DB::statement("ALTER TABLE visit_reports ALTER selling_price TYPE BIGINT USING 0 ");
    }
}
