<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MitraUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
           
        Schema::table( 'mitra', function ( Blueprint $table ) {
            $table->text( 'NPL')->nullable();
            $table->text( 'Jumlah_pegawai')->nullable();
            $table->text( 'Scoring')->nullable();
            $table->text( 'Ket_Scoring')->nullable();
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
