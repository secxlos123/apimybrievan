<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecontestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recontests', function (Blueprint $table) {
            $table->increments( 'id' );
            $table->integer( 'eform_id' )->unsigned();
            $table->string( 'purpose_of_visit' )->nullable();
            $table->text( 'pros' )->nullable();
            $table->text( 'cons' )->nullable();
            $table->text( 'ao_recommendation' )->nullable();
            $table->boolean( 'ao_recommended' )->nullable();
            $table->text( 'pinca_recommendation' )->nullable();
            $table->boolean( 'pinca_recommended' )->nullable();
            $table->date( 'expired_date' )->nullable();
            $table->text( 'documents' )->default( "{}" );
            $table->timestamps();

            $table->foreign( 'eform_id' )
                ->references( 'id' )->on( 'eforms' )
                ->onUpdate( 'cascade' )->onDelete( 'cascade' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recontests');
    }
}
