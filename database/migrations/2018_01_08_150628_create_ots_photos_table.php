<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtsPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ots_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ots_other_id')->nullable()->unsigned();
            $table->foreign('ots_other_id')->references('id')->on('ots_another_datas')->onUpdate( 'cascade' )->onDelete( 'cascade' );
            $table->string('image_data')->nullable();
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
        Schema::dropIfExists('ots_photos');
    }
}
