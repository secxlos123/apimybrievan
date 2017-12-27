<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Briguna extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
			Schema::create( 'briguna', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->integer( 'eform_id' );
            $table->string( 'status_property' );
            $table->integer( 'mitra_id' )->nullable();
            $table->integer( 'jenis_pinjaman_id' )->nullable();
            $table->double( 'price' );
            $table->integer( 'tujuan_penggunaan_id' );
            $table->text( 'year' );
            $table->double( 'request_amount' );
            $table->double( 'maksimum_plafond' );
            $table->double( 'angsuran_usulan' );

            $table->foreign( 'eform_id' )
                ->references( 'id' )->on( 'eforms' )
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
         Schema::dropIfExists( 'briguna' );
    }
}
