<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_types', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_id')->unsigned();
            $table->string('name');
            $table->string('surface_area');
            $table->string('building_area');
            $table->float('price', 11, 2);
            $table->string('electrical_power');
            $table->tinyInteger('bathroom');
            $table->tinyInteger('bedroom');
            $table->tinyInteger('floors');
            $table->tinyInteger('carport');
            $table->string('certificate')->nullable();
            $table->timestamps();

            $table->foreign( 'property_id' )->references( 'id' )->on( 'properties' )
                ->onUpdate( 'cascade' )
                ->onDelete( 'cascade' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_types');
    }
}
