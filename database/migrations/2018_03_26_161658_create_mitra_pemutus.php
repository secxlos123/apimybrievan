<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMitraPemutus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {  
          Schema::create( 'mitra_pemutus', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->text( 'pemutus_name' );
            $table->text( 'pemeriksa' );
            $table->text( 'jabatan' );
            $table->text( 'jabatan_pemeriksa' );
            $table->text( 'count_pemutus' );
            $table->text( 'id_header' );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'mitra_pemutus' );
    }
}






