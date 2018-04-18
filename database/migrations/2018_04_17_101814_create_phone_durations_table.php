<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneDurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketing_activity_phone_durations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pn');
            $table->string('nik')->default('null');
            $table->string('cif')->default('null');
            $table->string('phone_number');
            $table->string('duration');
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
        Schema::dropIfExists('marketing_activity_phone_durations');
    }
}
