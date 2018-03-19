<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_dates', function (Blueprint $table) {
            $table->increments('id');

            $table->integer( 'eform_id' );
            $table->string('action');
            $table->datetime('execute_at');

            $table->foreign( 'eform_id' )
                ->references( 'id' )->on( 'eforms' )
                ->onUpdate( 'cascade' )->onDelete( 'cascade' );

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
        Schema::dropIfExists('action_dates');
    }
}
