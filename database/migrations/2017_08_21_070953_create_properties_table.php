<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('developer_id')->unsigned()->nullable();
            $table->integer('city_id')->unsigned();
            $table->string('approved_by')->nullable();
            $table->string('prop_id_bri')->nullable();
            $table->string('name');
            $table->string('pic_name');
            $table->string('pic_phone');
            $table->text('address');
            $table->enum('category', [0, 1, 2]);
            $table->string('latitude');
            $table->string('longitude');
            $table->longText('facilities');
            $table->string('slug');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();

            $table->foreign( 'developer_id' )->references( 'id' )->on( 'developers' )
                ->onUpdate( 'cascade' )
                ->onDelete( 'set null' );
                
            $table->foreign( 'city_id' )->references( 'id' )->on( 'cities' )
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
        Schema::dropIfExists('properties');
    }
}
