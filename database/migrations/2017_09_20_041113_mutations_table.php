<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'mutations', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->integer( 'visit_report_id' )->nullable();
            $table->string( 'bank' )->nullable();
            $table->string( 'number' )->nullable();
            $table->string( 'file' )->nullable();

            $table->foreign( 'visit_report_id' )
                ->references( 'id' )->on( 'visit_reports' )
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
        Schema::dropIfExists( 'mutations' );
    }
}
