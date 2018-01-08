<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertGimmickDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
           Schema::create( 'gimmick_detail', function ( Blueprint $table ) {
            $table->increments( 'no' );
            $table->text( 'id_header' )->nullable();
            $table->text( 'first_month' )->nullable();
            $table->text( 'last_month' )->nullable();
            $table->text( 'persen_bunga' )->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
