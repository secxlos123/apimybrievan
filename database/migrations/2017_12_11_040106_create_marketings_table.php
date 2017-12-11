<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pn')->unsigned()->index();
            $table->string('product_type')->index();
            $table->string('activity_type');
            $table->string('target');
            $table->integer('account_id')->unsigned()->index();
            $table->string('status')->index();
            $table->string('target_closing_date');
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
        Schema::dropIfExists('marketings');
    }
}
