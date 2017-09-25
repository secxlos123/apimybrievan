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
            $table->integer( 'user_id' )->unsigned();
            $table->string( 'internal_id' )->nullable();
            $table->string( 'ao_id' )->nullable();
            $table->date( 'appointment_date' )->nullable();
            $table->string( 'branch_id' )->nullable();
            $table->string( 'longitude' )->nullable();
            $table->string( 'latitude' )->nullable();
            $table->string( 'nik' )->nullable();
            $table->string( 'ref_number' )->nullable();
            $table->string( 'product_type' )->nullable();
            $table->integer( 'prescreening_status' )->nullable();
            $table->boolean( 'is_approved' )->default( false );
            $table->text( 'pros' )->nullable();
            $table->text( 'cons' )->nullable();
            $table->text( 'additional_parameters' )->default( "{}" );
            $table->timestamps();

            /**
             * User who propose
             */
            $table->foreign( 'user_id' )->references( 'id' )->on( 'users' )->onUpdate( 'cascade' )->onDelete( 'cascade' );
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
