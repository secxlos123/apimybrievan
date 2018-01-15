<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketingMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketing_maps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category')->index();
            $table->string('district_name');
            $table->string('address');
            $table->string('city');
            $table->text('longitude')->default('null');
            $table->text('latitude')->default('null');
            $table->string('pot_account');
            $table->string('pot_fund');
            $table->string('pot_loan');
            $table->string('pot_transaction');
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
        Schema::dropIfExists('marketing_maps');
    }
}
