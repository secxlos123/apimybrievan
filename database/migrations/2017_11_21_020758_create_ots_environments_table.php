<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtsEnvironmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ots_environments', function (Blueprint $table) {
            $stringType = 'string';
            $nullable = 'nullable';
            $limit = 100;
            $table->increments('id');
            $table->integer('collateral_id')->nullable()->unsigned();
            $table->foreign('collateral_id')->references('id')->on('collaterals')->onDelete('CASCADE');
            $table->{$stringType}('designated_land', $limit)->{$nullable}();
            $table->{$stringType}('designated', $limit)->{$nullable}();
            $table->{$stringType}('other_designated', $limit)->{$nullable}();
            $table->{$stringType}('nearest_location', $limit)->{$nullable}();
            $table->{$stringType}('other_guide', $limit)->{$nullable}();
            $table->{$stringType}('transportation', $limit)->{$nullable}();
            $table->decimal('distance_from_transportation')->{$nullable}();
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
        Schema::dropIfExists('ots_environments');
    }
}
