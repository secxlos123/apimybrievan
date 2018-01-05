<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirrpc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create( 'dirrpc', function ( Blueprint $table ) {
            $table->increments( 'no' );
            $table->text( 'debt_name' )->nullable();
            $table->text( 'maintance' )->nullable();
            $table->text( 'action' )->nullable();
            $table->text( 'no' )->nullable();
            $table->text( 'pemutus_name' )->nullable();
            $table->text( 'jabatan' )->nullable();
            $table->text( 'pemeriksa' )->nullable();
            $table->text( 'jabatan_pemeriksa' )->nullable();
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
