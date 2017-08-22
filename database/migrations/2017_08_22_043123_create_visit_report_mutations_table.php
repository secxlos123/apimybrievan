<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitReportMutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'visit_report_mutations', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->integer( 'visit_report_id' );
            $table->date( 'date' );
            $table->double( 'amount', 15, 2 );
            $table->string( 'type' );
            $table->string( 'information' );

            $table->foreign( 'visit_report_id' )->references( 'id' )->on( 'visit_reports' )->onUpdate( 'cascade' )->onDelete( 'cascade' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'visit_report_mutations' );
    }
}
