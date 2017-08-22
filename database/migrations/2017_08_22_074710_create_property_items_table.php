<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertyItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('property_type_id')->unsigned();
            $table->text('address');
            $table->float('price', 11, 2);
            $table->enum('status', ['new', 'second']);
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->foreign( 'property_type_id' )->references( 'id' )->on( 'property_types' )
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
        Schema::dropIfExists('property_items');
    }
}
