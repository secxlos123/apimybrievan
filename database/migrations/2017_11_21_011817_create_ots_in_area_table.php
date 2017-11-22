<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtsInAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ots_in_areas', function (Blueprint $table) {
            $oneHundred = 100;
            $ten = 10;
            $six = 6;
            $zero = 0;
            $stringType = 'string';
            $nullable = 'nullable';
            $default = 'default';
            $integerType = 'integer';
            $floatType = 'float';
            $decimalType = 'decimal';
            $textType = 'text';
            $table->increments('id');
            $table->{$integerType}('collateral_id')->nullable()->unsigned();
            $table->foreign('collateral_id')->references('id')->on('collaterals')->onDelete('CASCADE');
            $table->{$stringType}('collateral_type')->{$nullable}();
            $table->{$integerType}('city_id')->{$nullable}()->unsigned();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('CASCADE');
            $table->{$textType}('location')->{$nullable}();
            $table->{$floatType}('latitude', $ten, $six)->{$nullable}();
            $table->{$floatType}('longtitude', $ten, $six)->{$nullable}();
            $table->{$stringType}('district', $oneHundred)->{$nullable}();
            $table->{$stringType}('sub_district', $oneHundred)->{$nullable}();
            $table->{$stringType}('rt', $oneHundred)->{$nullable}();
            $table->{$stringType}('rw', $oneHundred)->{$nullable}();
            $table->{$stringType}('zip_code', $oneHundred)->{$nullable}();
            $table->{$decimalType}('distance')->{$nullable}();
            $table->{$integerType}('unit_type')->{$nullable}()->{$default}($zero);
            $table->{$stringType}('distance_from')->{$nullable}();
            $table->{$stringType}('position_from_road', $oneHundred)->{$nullable}();
            $table->{$stringType}('ground_type', $oneHundred)->{$nullable}();
            $table->{$stringType}('ground_level', $oneHundred)->{$nullable}();
            $table->{$decimalType}('distance_of_position')->{$nullable}()->{$default}($zero);
            $table->{$stringType}('north_limit', $oneHundred)->{$nullable}();
            $table->{$stringType}('east_limit', $oneHundred)->{$nullable}();
            $table->{$stringType}('south_limit', $oneHundred)->{$nullable}();
            $table->{$stringType}('west_limit', $oneHundred)->{$nullable}();
            $table->{$textType}('another_information')->{$nullable}();
            $table->decimal('surface_area')->{$nullable}()->{$default}($zero);
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
        Schema::dropIfExists('ots_in_areas');
    }
}
