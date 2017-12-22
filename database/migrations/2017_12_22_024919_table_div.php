<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableDiv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'view_div', function ( Blueprint $table ) {
            $table->increments( 'index' );
            $table->text( 'class' )->nullable();
            $table->text( 'id' )->nullable();
            $table->text( 'name' )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists( 'view_div' );
    }
}
