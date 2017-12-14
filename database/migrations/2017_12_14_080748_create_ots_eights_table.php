<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtsEightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ots_eights', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('collateral_id')->nullable()->unsigned();
            $table->foreign('collateral_id')->references('id')->on('collaterals')->onDelete('CASCADE');
            $table->string('liquidation_realization')->nullable();
            $table->string('fair_market')->nullable();
            $table->string('liquidation')->nullable();
            $table->string('liquidation_projection')->nullable();
            $table->string('fair_market_projection')->nullable();
            $table->string('njop')->nullable();
            $table->string('appraisal_by')->nullable();
            $table->string('independent_appraiser')->nullable();
            $table->string('date_assessment')->nullable();
            $table->string('type_binding')->nullable();
            $table->string('binding_number')->nullable();
            $table->string('nilai pengikatan => binding_value')->nullable();
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
        Schema::dropIfExists('ots_eights');
    }
}
