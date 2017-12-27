<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
               Schema::create( 'view_table', function ( Blueprint $table ) {
            $table->increments( 'no' );
            $table->text( 'index' )->nullable();
            $table->text( 'view' )->nullable();
            $table->text( 'type' )->nullable();
            $table->text( 'class' )->nullable();
            $table->text( 'name' )->nullable();
            $table->text( 'id' )->nullable();
			$table->text( 'value' )->nullable();
            $table->text( 'etc' )->nullable();
            $table->integer( 'div' )->nullable();
            $table->integer( 'label' )->nullable();
        } );
 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::dropIfExists( 'view_table' );
    }
}
