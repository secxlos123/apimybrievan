<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category')->index();
            $table->string('market_name');
            $table->string('province')->index();
            $table->string('city')->index();
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
        Schema::dropIfExists('market_mappings');
    }
}
