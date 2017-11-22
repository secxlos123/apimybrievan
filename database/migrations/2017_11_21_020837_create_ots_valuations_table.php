<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtsValuationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ots_valuations', function (Blueprint $table) {
            $stringType = 'string';
            $nullable = 'nullable';
            $date = 'date';
            $limit = 100;
            $table->increments('id');
            $table->integer('collateral_id')->nullable()->unsigned();
            $table->foreign('collateral_id')->references('id')->on('collaterals')->onDelete('CASCADE');
            $table->{$date}('scoring_land_date')->{$nullable}();
            $table->{$stringType}('npw_land', $limit)->{$nullable}();
            $table->{$stringType}('nl_land', $limit)->{$nullable}();
            $table->{$stringType}('pnpw_land', $limit)->{$nullable}();
            $table->{$stringType}('pnl_land', $limit)->{$nullable}();
            $table->{$date}('scoring_building_date')->{$nullable}();
            $table->{$stringType}('npw_building', $limit)->{$nullable}();
            $table->{$stringType}('nl_building', $limit)->{$nullable}();
            $table->{$stringType}('pnpw_building', $limit)->{$nullable}();
            $table->{$stringType}('pnl_building', $limit)->{$nullable}();
            $table->{$date}('scoring_all_date')->{$nullable}();
            $table->{$stringType}('npw_all', $limit)->{$nullable}();
            $table->{$stringType}('nl_all', $limit)->{$nullable}();
            $table->{$stringType}('pnpw_all', $limit)->{$nullable}();
            $table->{$stringType}('pnl_all', $limit)->{$nullable}();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ots_valuations');
    }
}
