<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 150)->nullable();
            $table->date('appointment_date')->nullable();
            $table->date('appointment_date_res')->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('eform_id')->unsigned()->nullable();
            $table->string('ao_id')->nullable();
            $table->string('ref_number', 150)->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->longText('address')->nullable();
            $table->longText('desc')->nullable();
            $table->enum('status', ['rejected', 'waiting', 'approved'])->default('waiting');
            $table->timestamps();

            /**
             * User who propose
             */
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            // $table->foreign('eform_id')->references('id')->on('eforms')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
