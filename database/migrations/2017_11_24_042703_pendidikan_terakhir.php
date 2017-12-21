<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PendidikanTerakhir extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
           Schema::create( 'pendidikan_terakhir', function ( Blueprint $table ) {
            $table->increments( 'kode' );
            $table->text( 'keterangan' )->nullable;
			$table->foreign( 'kode' )
                ->references( 'kode' )->on( 'pendidikan_terakhir' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'pendidikan_terakhir' );
    }
}
