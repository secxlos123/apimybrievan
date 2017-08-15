<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEforms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'eforms', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->integer( 'user_id' );
            $table->integer( 'internal_id' )->nullable();
            $table->integer( 'ao_id' )->nullable();
            $table->date( 'appointment_date' )->nullable();
            $table->string( 'longitude' )->nullable();
            $table->string( 'latitude' )->nullable();
            $table->string( 'branch' )->nullable();
            $table->text( 'product' )->nullable();
            $table->integer( 'prescreening_status' )->nullable();
            $table->boolean( 'is_approved' )->default( false );
            $table->timestamps();

            $table->foreign( 'user_id' )->references( 'id' )->on( 'users' )->onUpdate( 'cascade' )->onDelete( 'cascade' );
            $table->foreign( 'ao_id' )->references( 'id' )->on( 'users' )->onUpdate( 'cascade' )->onDelete( 'set null' );
            $table->foreign( 'internal_id' )->references( 'id' )->on( 'users' )->onUpdate( 'cascade' )->onDelete( 'set null' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'eforms' );
    }
}
