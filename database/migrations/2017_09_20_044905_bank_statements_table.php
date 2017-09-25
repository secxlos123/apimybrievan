<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BankStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'bank_statements', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->integer( 'mutation_id' )->nullable();
            $table->date( 'date' )->nullable();
            $table->double( 'amount' )->nullable();
            $table->string( 'type' )->nullable();
            $table->text( 'note' )->nullable();

            $table->foreign( 'mutation_id' )
                ->references( 'id' )->on( 'mutations' )
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
        Schema::dropIfExists( 'bank_statements' );
    }
}
