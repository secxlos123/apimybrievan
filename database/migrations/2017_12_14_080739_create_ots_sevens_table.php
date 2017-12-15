<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtsSevensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ots_sevens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('collateral_id')->nullable()->unsigned();
            $table->foreign('collateral_id')->references('id')->on('collaterals')->onDelete('CASCADE');
            $table->string('collateral_status')->nullable();
            $table->string('on_behalf_of')->nullable();
            $table->string('ownership_number')->nullable();
            $table->string('location')->nullable();
            $table->string('address_collateral')->nullable();
            $table->text('description')->nullable();
            $table->string('ownership_status')->nullable();
            $table->date('date_evidence')->nullable();
            $table->string('village')->nullable();
            $table->string('districts')->nullable();
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
        Schema::dropIfExists('ots_sevens');
    }
}
