<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterKprDp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::unprepared("DROP VIEW IF EXISTS collateral_view_table");
        \DB::statement("ALTER TABLE kpr ALTER dp TYPE DOUBLE PRECISION USING dp::DOUBLE PRECISION ");
        DB::unprepared(file_get_contents(__DIR__. '/../csv/collateral-view.sql'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::unprepared("DROP VIEW IF EXISTS collateral_view_table");
        \DB::statement("ALTER TABLE kpr ALTER dp TYPE INTEGER USING round(dp)::integer ");
        DB::unprepared(file_get_contents(__DIR__. '/../csv/collateral-view.sql'));
    }
}
