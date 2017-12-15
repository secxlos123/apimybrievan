<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtsNinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ots_nines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('collateral_id')->nullable()->unsigned();
            $table->foreign('collateral_id')->references('id')->on('collaterals')->onDelete('CASCADE');
            $table->string('certificate_status')->nullable();
            $table->date('receipt_date')->nullable();
            $table->text('information')->nullable();
            $table->string('notary_status')->nullable();
            $table->string('takeover_status')->nullable();
            $table->string('credit_status')->nullable();
            $table->string('skmht_status')->nullable();
            $table->string('imb_status')->nullable();
            $table->string('shgb_status')->nullable();
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
        Schema::dropIfExists('ots_nines');
    }
}
