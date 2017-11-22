<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtsAnotherDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ots_another_datas', function (Blueprint $table) {
            $stringType = 'string';
            $nullable = 'nullable';
            $date = 'date';
            $limit = 100;
            $table->increments('id');
            $table->integer('collateral_id')->nullable()->unsigned();
            $table->foreign('collateral_id')->references('id')->on('collaterals')->onDelete('CASCADE');
            $table->{$stringType}('bond_type')->{$nullable}();
            $table->{$stringType}('use_of_building_function')->{$nullable}();
            $table->{$stringType}('optimal_building_use')->{$nullable}();
            $table->{$stringType}('building_exchange')->{$nullable}();
            $table->text('things_bank_must_know')->{$nullable};
            $table->{$stringType}('image_condition_area')->{$nullable}();
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
        Schema::dropIfExists('ots_another_datas');
    }
}
