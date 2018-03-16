<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColoumCustomerIsFinish extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::table( 'customer_details', function ( Blueprint $table ) {
            $table->text( 'IsFinish' )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            Schema::table( 'customer_details', function ( Blueprint $table ) {
            $table->text( 'IsFinish' )->nullable();
        } );
    }
}
