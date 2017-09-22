<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKpr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'kpr', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->integer( 'eform_id' );
            $table->enum( 'status_property', [ 'new', 'second' ] );
            $table->integer( 'developer_id' )->nullable();
            $table->integer( 'property_id' )->nullable();
            $table->double( 'price' );
            $table->integer( 'building_area' );
            $table->text( 'home_location' );
            $table->integer( 'year' );
            $table->integer( 'active_kpr' );
            $table->integer( 'dp' );
            $table->double( 'request_amount' );

            $table->foreign( 'eform_id' )
                ->references( 'id' )->on( 'eforms' )
                ->onUpdate( 'cascade' )->onDelete( 'cascade' );

            $table->foreign( 'developer_id' )
                ->references( 'id' )->on( 'users' )
                ->onUpdate( 'cascade' )->onDelete( 'cascade' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'kpr' );
    }
}
