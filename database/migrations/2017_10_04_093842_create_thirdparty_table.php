<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThirdpartyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_parties', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150);
            $table->longText('address');
            $table->unsignedInteger('city_id');
            $table->bigInteger('phone_number');
            $table->string('email', 50);
            $table->enum('is_actived', ['active', 'disabled'])->default('active');
            $table->timestamps();

            $table->foreign('city_id')
                ->references('id')->on('cities')
                ->onUpdate('cascade')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('third_parties');
    }
}
