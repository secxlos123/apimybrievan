<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtsTensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ots_tens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('collateral_id')->nullable()->unsigned();
            $table->foreign('collateral_id')->references('id')->on('collaterals')->onDelete('CASCADE');
            $table->string('paripasu')->nullable();
            $table->string('paripasu_bank')->nullable();
            $table->string('insurance')->nullable();
            $table->string('insurance_company')->nullable();
            $table->string('insurance_value')->nullable();
            $table->string('eligibility')->nullable();
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
        Schema::dropIfExists('ots_tens');
    }
}
