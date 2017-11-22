<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtsBuildingDescsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ots_building_descs', function (Blueprint $table) {
            $stringType = 'string';
            $decimalType = 'decimal';
            $nullable = 'nullable';
            $default = 'default';
            $limit = 100;
            $table->increments('id');
            $table->integer('collateral_id')->nullable()->unsigned();
            $table->foreign('collateral_id')->references('id')->on('collaterals')->onDelete('CASCADE');
            $table->{$stringType}('permit_number', $limit)->{$nullable}();
            $table->date('permit_date')->{$nullable}();
            $table->{$stringType}('on_behalf_of', $limit)->{$nullable}();
            $table->{$stringType}('type', $limit)->{$nullable}();
            $table->{$stringType}('count', $limit)->{$nullable}();
            $table->{$stringType}('spacious', $limit)->{$nullable}();
            $table->{$stringType}('year', $limit)->{$nullable}();
            $table->text('description')->{$nullable}();
            $table->{$decimalType}('north_limit')->{$nullable}()->{$default}(0);
            $table->{$stringType}('north_limit_from', $limit)->{$nullable}();
            $table->{$decimalType}('east_limit')->{$nullable}()->{$default}(0);
            $table->{$stringType}('east_limit_from', $limit)->{$nullable}();
            $table->{$decimalType}('south_limit')->{$nullable}()->{$default}(0);
            $table->{$stringType}('south_limit_from', $limit)->{$nullable}();
            $table->{$decimalType}('west_limit')->{$nullable}()->{$default}(0);
            $table->{$stringType}('west_limit_from', $limit)->{$nullable}();
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
        Schema::dropIfExists('ots_building_descs');
    }
}
