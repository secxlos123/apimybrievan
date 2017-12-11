<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGimmickSukubunga extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create( 'gimmick_sukubunga', function ( Blueprint $table ) {
            $table->integer( 'id_gimmick' )->nullable();
            $table->text( 'first_month' )->nullable();
            $table->text( 'last_month' )->nullable();
            $table->text( 'suku_bunga' )->nullable();
			$table->foreign('id_gimmick')->references('id_gimmick')->on('gimmick_kredit');
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'gimmick_sukubunga' );
    }

}
