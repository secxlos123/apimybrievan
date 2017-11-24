<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JenisPinjaman extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create( 'jenis_pinjaman', function ( Blueprint $table ) {
            $table->increments( 'kode' );
            $table->text( 'keterangan' )->nullable();
			$table->foreign( 'kode' )
                ->references( 'kode' )->on( 'jenis_pinjaman' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'jenis_pinjaman' );
    }
}
